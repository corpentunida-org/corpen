<?php

namespace App\Http\Controllers\Demografia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\Demografia\DemografiaExport;
use App\Imports\Demografia\DemografiaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

// IMPORTANTE: Modelos necesarios para las consultas del Dashboard y el Index
use App\Models\Demografia\Pais;
use App\Models\Demografia\Region;
use App\Models\Demografia\Ciudad;
use App\Models\Demografia\Direccion;

class DemografiaController extends Controller
{
    // ==========================================
    //   0. DASHBOARD E INDICADORES
    // ==========================================
    public function dashboard()
    {
        // Consultar los KPIs reales de la base de datos
        $totalPaises = Pais::count();
        $totalRegiones = Region::count();
        $totalCiudades = Ciudad::count();
        $totalDirecciones = Direccion::count();

        // Enviar las variables a la vista
        return view('demograficos.dashboard', compact(
            'totalPaises', 
            'totalRegiones', 
            'totalCiudades', 
            'totalDirecciones'
        ));
    }

    // ==========================================
    //   1. FLUJO DE SINCRONIZACIÓN MASIVA
    // ==========================================
    
    // Muestra la vista principal de importación/exportación
    public function excelIndex()
    {
        return view('demograficos.sincronizar');
    }

    // Acción: Descarga la BD completa en múltiples hojas
    public function descargarExcel()
    {
        return Excel::download(new DemografiaExport(), 'demografia_maestra.xlsx');
    }

    // Paso 1: Sube el archivo y lo deja "en espera" (Sesión/Storage)
    public function subirExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            // Guardar el archivo temporalmente en la carpeta local 'imports'
            $path = $request->file('archivo_excel')->store('imports');

            // Guardar la ruta en la sesión para el paso 2
            session()->put('excel_demografia_path', $path);

            return redirect()->route('demograficos.sincronizar.index')
                ->with('warning', 'Archivo cargado correctamente. Por favor, confirma la sincronización para afectar la base de datos.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Error al cargar el archivo: ' . $e->getMessage()]);
        }
    }

    // Paso 2: Ejecuta la importación a la BD y limpia el archivo temporal
    public function confirmarSincronizacion(Request $request)
    {
        // Recuperar la ruta del archivo desde la sesión
        $path = session('excel_demografia_path');

        if (!$path || !Storage::exists($path)) {
            return redirect()->route('demograficos.sincronizar.index')
                ->withErrors(['No se encontró ningún archivo pendiente para sincronizar.']);
        }

        try {
            // Ejecutar la importación (Paises, Regiones, Subregiones, Ciudades, Direcciones)
            Excel::import(new DemografiaImport(), $path);

            // Limpieza: Eliminar archivo temporal y la variable de sesión
            Storage::delete($path);
            session()->forget('excel_demografia_path');

            return redirect()->route('demograficos.sincronizar.index')
                ->with('success', '¡Sincronización masiva completada con éxito!');

        } catch (\Exception $e) {
            // En caso de error de BD, mantener el archivo por si se quiere reintentar
            return redirect()->route('demograficos.sincronizar.index')
                ->withErrors(['Error crítico durante la sincronización: ' . $e->getMessage()]);
        }
    }


    // ==========================================
    //   2. GESTIÓN DEL MAESTRO (CRUD MANUAL)
    // ==========================================
    
    public function index(Request $request)
    {
        // Traer los países paginados y contar cuántas regiones tiene cada uno
        $paises = Pais::withCount('regiones')->paginate(10);
        
        return view('demograficos.maestro.index', compact('paises'));
    }

    public function create()
    {
        return view('demograficos.maestro.create');
    }

    public function store(Request $request)
    {
        // Lógica de guardado manual
    }

    public function show($id)
    {
        // Busca el país por su código ISO y precarga sus regiones
        $pais = Pais::with('regiones')->findOrFail($id);

        return view('demograficos.maestro.show', compact('pais'));
    }

    public function edit($id)
    {
        return view('demograficos.maestro.edit');
    }

    public function update(Request $request, $id)
    {
        // Lógica de actualización manual
    }

    public function destroy($id)
    {
        // Lógica de eliminación
    }
}