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
        $validated = $request->validate([
            'cod_ter_MaeTerceros'     => 'required|integer',
            'monto_pagado'            => 'required|integer',
            'fecha_pago'              => 'required|integer', 
            'archivo_soporte'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hash_transaccion'        => 'nullable|string|unique:car_comprobantes_pagos,hash_transaccion',
            'id_transaccion_bancaria' => 'nullable|integer',
            'id_interaction'          => 'required|integer',
        ]);

        // Construcción de la ruta dinámica: cartera/comprobantes/12345
        $folderPath = "cartera/comprobantes/{$validated['cod_ter_MaeTerceros']}";

        $rutaArchivo = null;
        if ($request->hasFile('archivo_soporte')) {
            // El método store() devuelve la ruta completa incluyendo el nombre generado
            $rutaArchivo = $request->file('archivo_soporte')->store($folderPath, 's3');
        }

        CarComprobantePago::create([
            'cod_ter_MaeTerceros'     => $validated['cod_ter_MaeTerceros'],
            'monto_pagado'            => $validated['monto_pagado'],
            'fecha_pago'              => $validated['fecha_pago'],
            'hash_transaccion'        => $validated['hash_transaccion'],
            'ruta_archivo'            => $rutaArchivo,
            'id_transaccion_bancaria' => $validated['id_transaccion_bancaria'],
            'id_interaction'          => $validated['id_interaction'],
            'id_user'                 => auth()->id(), // Seguridad: ID del usuario autenticado
            'estado'                  => 'pendiente', 
        ]);

        return redirect()->route('cartera.comprobantes.index')
                        ->with('success', 'Soporte almacenado correctamente en la carpeta del tercero.');
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