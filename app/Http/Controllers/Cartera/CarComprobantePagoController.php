<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Models\Cartera\CarComprobantePago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Creditos\LineaCredito;
use App\Models\Contabilidad\ConCuentaBancaria;

class CarComprobantePagoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtro Obligatorio: Periodo (Año y Mes). Por defecto el mes actual.
        $periodo = $request->input('periodo', date('Y-m'));
        $parts = explode('-', $periodo);
        $year = $parts[0] ?? date('Y');
        $month = $parts[1] ?? date('m');

        // 2. Consulta Base
        $query = CarComprobantePago::whereYear('fecha_pago', $year)
                                   ->whereMonth('fecha_pago', $month);

        // 3. Filtrar por estado (Tabs de la interfaz)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 4. Buscador backend (busca en toda la base de datos y ahora incluye los nuevos campos)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_interaction', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_obligacion', 'LIKE', "%{$busqueda}%") 
                  ->orWhere('monto_pagado', 'LIKE', "%{$busqueda}%")
                  ->orWhere('numero_cuota', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pr', 'LIKE', "%{$busqueda}%")
                  ->orWhere('cco', 'LIKE', "%{$busqueda}%");
            });
        }

        // 5. Paginación robusta de 100 registros
        $comprobantes = $query->latest()->paginate(100)->withQueryString(); 
        
        return view('cartera.comprobantes.index', compact('comprobantes', 'periodo'));
    }

    public function create()
    {
        // ¡CRÍTICO PARA PRODUCCIÓN! Consultas necesarias para poblar los select de la vista
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');
        $idBanco = ConCuentaBancaria::select('id', 'numero_cuenta','banco')->get();

        return view('cartera.comprobantes.create', compact('lineasCredito', 'idBanco'));
    }

    public function store(Request $request)
    {
        // 1. Preprocesar fecha
        if ($request->filled('fecha_pago')) {
            $request->merge(['fecha_pago' => str_replace('-', '', $request->fecha_pago)]);
        }

        // [CORRECCIÓN CRÍTICA]: Convertir strings vacíos a NULL
        $request->merge([
            'pr'           => $request->filled('pr') ? $request->pr : null,
            'cco'          => $request->filled('cco') ? $request->cco : null,
            'numero_cuota' => $request->filled('numero_cuota') ? $request->numero_cuota : null,
        ]);

        $validated = $request->validate([
            'cod_ter_MaeTerceros'     => 'required|integer',
            'id_obligacion'           => 'nullable|integer', 
            'monto_pagado'            => 'required|numeric',
            'fecha_pago'              => 'required|integer', 
            'archivo_soporte'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'id_banco'                => 'required|integer',
            'temp_token'              => 'nullable|string|max:255',
            'id_interaction'          => 'nullable|integer',
            'pr'                      => 'nullable|integer', 
            'cco'                     => 'nullable|integer', 
            'numero_cuota'            => 'nullable|integer', 
        ]);

        try {
            $hash_transaccion = $validated['id_banco'] . '-' . $validated['fecha_pago'] . '-' . $validated['monto_pagado'] . '-' . $validated['cod_ter_MaeTerceros'];
            
            $existeHash = CarComprobantePago::where('hash_transaccion', $hash_transaccion)->exists();

            // VALIDACIÓN DE DUPLICADO CON FORZADO
            if ($existeHash && !$request->boolean('force_save')) {
                return response()->json([
                    'success' => false,
                    'is_duplicate' => true,
                    'message' => 'Ya existe un pago con estos mismos datos. ¿Deseas registrarlo de todas formas?'
                ]);
            }

            // Si se fuerza, alteramos el hash para evitar error de UNIQUE en base de datos
            if ($existeHash && $request->boolean('force_save')) {
                $hash_transaccion .= '-F-' . time();
            }

            $rutaArchivo = null;
            if ($request->hasFile('archivo_soporte')) {
                $folderPath = "corpentunida/cartera/comprobantes/{$validated['cod_ter_MaeTerceros']}";
                $rutaArchivo = $request->file('archivo_soporte')->store($folderPath, 's3');
            }

            CarComprobantePago::create([
                'cod_ter_MaeTerceros'     => $validated['cod_ter_MaeTerceros'],
                'id_obligacion'           => $validated['id_obligacion'] ?? null, 
                'monto_pagado'            => $validated['monto_pagado'],
                'fecha_pago'              => $validated['fecha_pago'],
                'hash_transaccion'        => $hash_transaccion,
                'ruta_archivo'            => $rutaArchivo,
                'id_interaction'          => $validated['id_interaction'] ?? 0,
                'temp_token'              => $validated['temp_token'] ?? null, 
                'id_user'                 => auth()->id(), 
                'estado'                  => 'pendiente', 
                'id_banco'                => $validated['id_banco'], 
                'pr'                      => $validated['pr'],
                'cco'                     => $validated['cco'],
                'numero_cuota'            => $validated['numero_cuota']
            ]);

            return response()->json(['success' => true, 'message' => 'Soporte almacenado correctamente.']);

        } catch (\Exception $e) {
            \Log::error("Error en CarComprobantePago@store: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);

        $validated = $request->validate([
            'monto_pagado'   => 'sometimes|integer',
            'id_obligacion'  => 'sometimes|integer', 
            'estado'         => 'sometimes|in:pendiente,conciliado,rechazado',
            'archivo_soporte'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pr'             => 'sometimes|nullable|integer', // NUEVO
            'cco'            => 'sometimes|nullable|integer', // NUEVO
            'numero_cuota'   => 'sometimes|nullable|integer', // NUEVO
        ]);

        if ($request->hasFile('archivo_soporte')) {
            $terceroId = $comprobante->cod_ter_MaeTerceros;
            $folderPath = "cartera/comprobantes/{$terceroId}";

            // Borrar el archivo anterior de S3
            if ($comprobante->ruta_archivo) {
                Storage::disk('s3')->delete($comprobante->ruta_archivo);
            }

            $validated['ruta_archivo'] = $request->file('archivo_soporte')->store($folderPath, 's3');
        }

        $comprobante->update($validated);

        return redirect()->route('cartera.comprobantes.index')->with('success', 'Registro actualizado.');
    }

    public function show($id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);
        return view('cartera.comprobantes.show', compact('comprobante'));
    }

    public function destroy($id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);
        
        // Eliminar el archivo físico de S3 al borrar el registro
        if ($comprobante->ruta_archivo && Storage::disk('s3')->exists($comprobante->ruta_archivo)) {
            Storage::disk('s3')->delete($comprobante->ruta_archivo);
        }

        $comprobante->delete();

        return redirect()->route('cartera.comprobantes.index')
                         ->with('success', 'Comprobante y archivo eliminados.');
    }
}