<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Models\Cartera\CarComprobantePago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Creditos\LineaCredito;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Support\Facades\Auth;

class CarComprobantePagoController extends Controller
{
    /**
     * Muestra el listado de comprobantes de pago.
     * Incluye filtros por periodo, estado, modo global y un buscador general.
     */
    public function index(Request $request)
    {
        // 1. Recibir parámetros de la URL
        $periodo = $request->input('periodo', date('Y-m'));
        $is_global = $request->has('is_global'); // Switch para buscar en todo el histórico

        // 2. Consulta base con Eager Loading Optimizado
        $query = CarComprobantePago::with([
            'tercero:cod_ter,nom_ter',
            'user:id,name',
            'obligacion:id,nombre',
            'banco:id,banco'
        ]);

        // 3. Lógica de Periodo (Solo aplica si NO estamos en "Modo Global")
        if (!$is_global) {
            $parts = explode('-', $periodo);
            $year = $parts[0] ?? date('Y');
            $month = $parts[1] ?? date('m');

            $query->whereYear('fecha_pago', $year)
                  ->whereMonth('fecha_pago', $month);
        }

        // 4. Filtro por estado (Pendiente, Conciliado, Rechazado)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 5. Buscador Global
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_interaction', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_obligacion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('monto_pagado', 'LIKE', "%{$busqueda}%")
                  ->orWhere('numero_cuota', 'LIKE', "%{$busqueda}%")
                  ->orWhere('hasta_cuota', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pr', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cco', 'LIKE', "%{$busqueda}%")
                  ->orWhere('tipo_pago', 'LIKE', "%{$busqueda}%")
                  ->orWhere('observacion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('hash_transaccion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_transaccion_bancaria', 'LIKE', "%{$busqueda}%");
            });
        }

        // =========================================================================
        // 5.1 SECCIÓN DE CÁLCULO DE KPIs (Basado en la misma consulta filtrada)
        // Usamos (clone $query) para no afectar la paginación posterior.
        // =========================================================================
        $kpiQuery = clone $query;

        $totalRegistros = $kpiQuery->count();
        $montoTotal     = (clone $kpiQuery)->sum('monto_pagado');
        $totalPendientes = (clone $kpiQuery)->where('estado', 'pendiente')->count();
        $totalConciliados = (clone $kpiQuery)->where('estado', 'conciliado')->count();

        // 6. Paginación directa y ligera
        $comprobantes = $query->latest('id')->paginate(10)->withQueryString();

        return view('cartera.comprobantes.index', compact(
            'comprobantes',
            'periodo',
            'is_global',
            'totalRegistros',
            'montoTotal',
            'totalPendientes',
            'totalConciliados'
        ));
    }

    /**
     * Muestra el formulario para crear un nuevo comprobante.
     */
    public function create()
    {
        // Consultas necesarias para poblar los selects (desplegables) de la vista
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');
        $idBanco = ConCuentaBancaria::select('id', 'numero_cuenta','banco')->get();

        return view('cartera.comprobantes.create', compact('lineasCredito', 'idBanco'));
    }

    /**
     * Procesa y guarda un nuevo comprobante en la base de datos y AWS S3.
     */
    public function store(Request $request)
    {
        // 1. Preprocesar fecha para que sea un número puro de 14 dígitos (YYYYMMDDHHMMSS)
        if ($request->filled('fecha_pago')) {
            $soloNumeros = preg_replace('/[^0-9]/', '', $request->fecha_pago);

            if (strlen($soloNumeros) === 8) {
                $soloNumeros .= date('His');
            }
            elseif (strlen($soloNumeros) === 12) {
                $soloNumeros .= '00';
            }

            $request->merge(['fecha_pago' => $soloNumeros]);
        }

        // 2. Convertir strings vacíos a NULL para evitar errores de tipo en BD
        $request->merge([
            'pr'           => $request->filled('pr') ? $request->pr : null,
            'cco'          => $request->filled('cco') ? $request->cco : null,
            'numero_cuota' => $request->filled('numero_cuota') ? $request->numero_cuota : null,
            // "Hasta Cuota": si no llega (pago de una sola cuota), cae al mismo valor de
            // "Desde" (numero_cuota) — el front ya lo autocompleta, esto es un respaldo por si
            // llega vacío igual (ej. JS deshabilitado).
            'hasta_cuota'  => $request->filled('hasta_cuota') ? $request->hasta_cuota : $request->numero_cuota,
            'tipo_pago'    => $request->filled('tipo_pago') ? $request->tipo_pago : null,
            'observacion'  => $request->filled('observacion') ? $request->observacion : null,
        ]);

        // 3. Validación estricta de los datos entrantes
        $validated = $request->validate([
            'cod_ter_MaeTerceros'     => 'required|integer',
            'id_obligacion'           => 'nullable|integer',
            'monto_pagado'            => 'required|numeric',
            'fecha_pago'              => 'required|numeric',
            'archivo_soporte'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Máx 5MB
            'id_banco'                => 'required|integer',
            'temp_token'              => 'nullable|string|max:255',
            'id_interaction'          => 'nullable|integer',
            'pr'                      => 'nullable|integer',
            'cco'                     => 'nullable|integer',
            'numero_cuota'            => 'nullable|integer',
            'hasta_cuota'             => 'nullable|integer|gte:numero_cuota',
            'tipo_pago'               => 'nullable|string|max:100',
            'observacion'             => 'nullable|string|max:255',
        ]);

        try {
            // 4. Generación del hash único de transacción para evitar duplicados
            $hash_transaccion = $validated['id_banco'] . '-' .
                                $validated['fecha_pago'] . '-' .
                                $validated['monto_pagado'] . '-' .
                                $validated['cod_ter_MaeTerceros'];

            $existeHash = CarComprobantePago::where('hash_transaccion', $hash_transaccion)->exists();

            // 5. Lógica de Prevención de Duplicados
            if ($existeHash && !$request->boolean('force_save')) {
                // Si existe pero el usuario no ha confirmado forzar, devolvemos alerta
                return response()->json([
                    'success' => false,
                    'is_duplicate' => true,
                    'message' => 'Ya existe un pago con estos mismos datos en este segundo exacto. ¿Deseas registrarlo de todas formas?'
                ]);
            }

            // Si el usuario decide forzar el guardado, alteramos el hash para que MySQL no lance error UNIQUE
            if ($existeHash && $request->boolean('force_save')) {
                $hash_transaccion .= '-F-' . time();
            }

            // 6. Subida de Archivo a AWS S3
            $rutaArchivo = null;
            if ($request->hasFile('archivo_soporte')) {
                // Organiza los archivos por el ID del tercero
                $folderPath = "corpentunida/cartera/comprobantes/{$validated['cod_ter_MaeTerceros']}";
                $rutaArchivo = $request->file('archivo_soporte')->store($folderPath, 's3');
            }

            // 7. Guardado en Base de Datos
            CarComprobantePago::create([
                'cod_ter_MaeTerceros'     => $validated['cod_ter_MaeTerceros'],
                'id_obligacion'           => $validated['id_obligacion'] ?? null,
                'monto_pagado'            => $validated['monto_pagado'],
                'fecha_pago'              => $validated['fecha_pago'],
                'hash_transaccion'        => $hash_transaccion,
                'ruta_archivo'            => $rutaArchivo,
                'id_interaction'          => $validated['id_interaction'] ?? 0,
                'id_transaccion_bancaria' => $request->filled('id_transaccion_bancaria')
                                            ? $request->id_transaccion_bancaria
                                            : [], // El mutator en el modelo se encarga de convertirlo a JSON
                'temp_token'              => $validated['temp_token'] ?? null,
                'id_user'                 => Auth::id(), // Registra el agente actual
                'estado'                  => 'pendiente', // Estado inicial por defecto
                'id_banco'                => $validated['id_banco'],
                'pr'                      => $validated['pr'],
                'cco'                     => $validated['cco'],
                'numero_cuota'            => $validated['numero_cuota'],
                'hasta_cuota'             => $validated['hasta_cuota'] ?? $validated['numero_cuota'],
                'tipo_pago'               => $validated['tipo_pago'],
                'observacion'             => $validated['observacion']
            ]);

            return response()->json(['success' => true, 'message' => 'Soporte almacenado correctamente.']);

        } catch (\Exception $e) {
            // Log para debuggeo en producción
            \Log::error("Error en CarComprobantePago@store: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza la información de un comprobante existente.
     */
    public function update(Request $request, $id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);

        $validated = $request->validate([
            'monto_pagado'   => 'sometimes|integer',
            'id_obligacion'  => 'sometimes|integer',
            'estado'         => 'sometimes|in:pendiente,conciliado,rechazado',
            'archivo_soporte'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pr'             => 'sometimes|nullable|integer',
            'cco'            => 'sometimes|nullable|integer',
            'numero_cuota'   => 'sometimes|nullable|integer',
            'hasta_cuota'    => 'sometimes|nullable|integer',
        ]);

        // Si se sube un archivo nuevo, borramos el viejo de S3
        if ($request->hasFile('archivo_soporte')) {
            $terceroId = $comprobante->cod_ter_MaeTerceros;
            $folderPath = "cartera/comprobantes/{$terceroId}";

            // Borrar el archivo anterior
            if ($comprobante->ruta_archivo) {
                Storage::disk('s3')->delete($comprobante->ruta_archivo);
            }

            // Guardar el nuevo
            $validated['ruta_archivo'] = $request->file('archivo_soporte')->store($folderPath, 's3');
        }

        $comprobante->update($validated);

        return redirect()->route('cartera.comprobantes.index')->with('success', 'Registro actualizado.');
    }

    /**
     * Muestra el detalle de un comprobante específico.
     */
    public function show($id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);
        return view('cartera.comprobantes.show', compact('comprobante'));
    }

    /**
     * Elimina un comprobante de la BD y su archivo físico en S3.
     */
    public function destroy($id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);

        // Limpieza del almacenamiento en S3
        if ($comprobante->ruta_archivo && Storage::disk('s3')->exists($comprobante->ruta_archivo)) {
            Storage::disk('s3')->delete($comprobante->ruta_archivo);
        }

        $comprobante->delete();

        return redirect()->route('cartera.comprobantes.index')
                         ->with('success', 'Comprobante y archivo eliminados.');
    }
}
