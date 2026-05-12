<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaccionesExport;
use App\Imports\TransaccionesImport;
use Illuminate\Support\Facades\Log;

class ExcelSyncController extends Controller
{
    public function index()
    {
        return view('contabilidad.extractos.sincronizar');
    }

    public function descargarExcel() 
    {
        return Excel::download(new TransaccionesExport, 'Data_Transacciones.xlsx');
    }

    public function subirExcel(Request $request) 
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new TransaccionesImport, $request->file('archivo_excel'));
            return back()->with('success', '¡Base de datos sincronizada correctamente!');
        } catch (\Exception $e) {
            // Guardamos el error en el log por si necesitas revisarlo a fondo
            Log::error("Error en importación: " . $e->getMessage());
            return back()->withErrors('Error al sincronizar: ' . $e->getMessage());
        }
    }
}