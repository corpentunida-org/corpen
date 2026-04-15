<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Models\Cartera\CarComprobantePago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarComprobantePagoController extends Controller
{
    public function index(Request $request)
    {
        $query = CarComprobantePago::query();

        // 1. Filtrar por estado (Tabs de la interfaz)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 2. Buscador backend (busca en toda la base de datos)
        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$busqueda}%")
                  ->orWhere('id_interaction', 'LIKE', "%{$busqueda}%")
                  ->orWhere('monto_pagado', 'LIKE', "%{$busqueda}%");
            });
        }

        // 3. EL ARREGLO ESTÁ AQUÍ: Usamos paginate() en lugar de get()
        // Esto devuelve un objeto LengthAwarePaginator, el cual sí tiene el método hasPages()
        $comprobantes = $query->latest()->paginate(15); 
        
        return view('cartera.comprobantes.index', compact('comprobantes'));
    }

    public function create()
    {
        return view('cartera.comprobantes.create');
    }

    public function store(Request $request)
    {
        // 1. Preprocesar fecha
        if ($request->filled('fecha_pago')) {
            $request->merge([
                'fecha_pago' => str_replace('-', '', $request->fecha_pago)
            ]);
        }

        // 2. Validar (Laravel enviará error JSON 422 si falla y es AJAX automáticamente)
        $validated = $request->validate([
            'cod_ter_MaeTerceros'     => 'required|integer',
            'monto_pagado'            => 'required|numeric',
            'fecha_pago'              => 'required|integer', 
            'archivo_soporte'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hash_transaccion'        => 'nullable|string',
            'id_transaccion_bancaria' => 'nullable|integer',
            'id_interaction'          => 'nullable|integer',
        ]);

        try {
            // 3. Generar Hash y comprobar duplicados
            $hash_transaccion = $validated['fecha_pago'] . '-' . $validated['monto_pagado'] . '-' . $validated['cod_ter_MaeTerceros'];
            
            $existeHash = CarComprobantePago::where('hash_transaccion', $hash_transaccion)->exists();

            if ($existeHash) {
                $msg = 'Este comprobante (misma fecha, monto y tercero) ya fue registrado previamente.';
                if ($request->ajax()) {
                    // IMPORTANTE: Devolvemos error 422 para que el JS sepa que no debe cerrar el modal
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->withInput()->withErrors(['hash_transaccion' => $msg]);
            }

            // 4. Subir archivo a S3
            $rutaArchivo = null;
            if ($request->hasFile('archivo_soporte')) {
                $folderPath = "cartera/comprobantes/{$validated['cod_ter_MaeTerceros']}";
                $rutaArchivo = $request->file('archivo_soporte')->store($folderPath, 's3');
            }

            // 5. Crear registro
            CarComprobantePago::create([
                'cod_ter_MaeTerceros'     => $validated['cod_ter_MaeTerceros'],
                'monto_pagado'            => $validated['monto_pagado'],
                'fecha_pago'              => $validated['fecha_pago'],
                'hash_transaccion'        => $hash_transaccion,
                'ruta_archivo'            => $rutaArchivo,
                'id_transaccion_bancaria' => $validated['id_transaccion_bancaria'] ?? 0,
                'id_interaction'          => $validated['id_interaction'] ?? 0,
                'id_user'                 => auth()->id(), 
                'estado'                  => 'pendiente', 
            ]);

            // 6. Respuesta para AJAX (Modal)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Soporte almacenado correctamente.'
                ]);
            }

            // Respuesta para formularios normales
            return redirect()->route('cartera.comprobantes.index')
                            ->with('success', 'Soporte almacenado correctamente.');

        } catch (\Exception $e) {
            // Si algo falla (S3, DB, etc.)
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al procesar: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error inesperado']);
        }
    }

    public function update(Request $request, $id)
    {
        $comprobante = CarComprobantePago::findOrFail($id);

        $validated = $request->validate([
            'monto_pagado'   => 'sometimes|integer',
            'estado'         => 'sometimes|in:pendiente,conciliado,rechazado',
            'archivo_soporte'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('archivo_soporte')) {
            // Al actualizar, mantenemos la carpeta del tercero original o la nueva si cambió
            $terceroId = $comprobante->cod_ter_MaeTerceros;
            $folderPath = "cartera/comprobantes/{$terceroId}";

            // Borrar el archivo anterior de S3 para no dejar basura
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