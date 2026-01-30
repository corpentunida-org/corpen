<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCargo;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoEmpleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GdoCargoController extends Controller
{
    /**
     * Muestra la lista de cargos con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $query = GdoCargo::with(['gdoArea', 'empleado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_cargo', 'LIKE', '%' . $search . '%')
                  ->orWhere('id', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('gdoArea', fn($q) => $q->where('nombre', 'LIKE', "%{$search}%"))
                  ->orWhereHas('empleado', fn($q) => $q->where('nombre1', 'LIKE', "%{$search}%")
                                                     ->orWhere('apellido1', 'LIKE', "%{$search}%")
                                                     ->orWhere('cedula', 'LIKE', "%{$search}%"));
            });
        }

        $cargos = $query->latest()->paginate(10);

        return view('archivo.cargo.index', compact('cargos'));
    }

    /**
     * Formulario de creación de cargo.
     */
    public function create()
    {
        $areas = GdoArea::orderBy('nombre')->get();
        $empleados = GdoEmpleado::all();
        $cargo = new GdoCargo();

        return view('archivo.cargo.create', compact('areas', 'empleados', 'cargo'));
    }

    /**
     * Almacena un nuevo cargo y sube el manual a S3.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_cargo'         => 'required|string|max:255',
            'salario_base'         => 'nullable|numeric|min:0',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            'manual_funciones'     => 'nullable|file|mimes:pdf|max:5120', 
            'GDO_area_id'          => 'nullable|exists:gdo_area,id',
            'GDO_empleados_cedula' => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);

        if ($request->hasFile('manual_funciones')) {
            $file = $request->file('manual_funciones');
            $filename = 'MANUAL_' . Str::slug($request->nombre_cargo) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs("archivo/cargos/manuales", $filename, 's3');
            $validated['manual_funciones'] = $path;
        }

        GdoCargo::create($validated);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo creado correctamente.');
    }

    /**
     * Muestra el detalle del cargo.
     */
    public function show(GdoCargo $cargo)
    {
        $cargo->load(['gdoArea', 'empleado']);
        return view('archivo.cargo.show', compact('cargo'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(GdoCargo $cargo)
    {
        $cargo->load('empleado'); 
        $areas = GdoArea::orderBy('nombre')->get();
        $empleados = GdoEmpleado::all();

        return view('archivo.cargo.edit', compact('cargo', 'areas', 'empleados'));
    }

    /**
     * Actualiza el cargo y gestiona el archivo en S3.
     */
    public function update(Request $request, GdoCargo $cargo)
    {
        $validated = $request->validate([
            'nombre_cargo'         => 'required|string|max:255',
            'salario_base'         => 'nullable|numeric',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            'manual_funciones'     => 'nullable|file|mimes:pdf|max:5120',
            'GDO_area_id'          => 'nullable|exists:gdo_area,id',
            'GDO_empleados_cedula' => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);

        if ($request->hasFile('manual_funciones')) {
            if ($cargo->manual_funciones && Storage::disk('s3')->exists($cargo->manual_funciones)) {
                Storage::disk('s3')->delete($cargo->manual_funciones);
            }

            $file = $request->file('manual_funciones');
            $filename = 'MANUAL_' . Str::slug($request->nombre_cargo) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs("archivo/cargos/manuales", $filename, 's3');
            $validated['manual_funciones'] = $path;
        }

        $cargo->update($validated);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo actualizado correctamente.');
    }

    /**
     * Elimina el cargo y su manual asociado en S3.
     */
    public function destroy(GdoCargo $cargo)
    {
        if ($cargo->manual_funciones && Storage::disk('s3')->exists($cargo->manual_funciones)) {
            Storage::disk('s3')->delete($cargo->manual_funciones);
        }

        $cargo->delete();
        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo eliminado.');
    }

    /**
     * Genera una URL temporal para visualizar el manual PDF en S3.
     */
    public function verManual(GdoCargo $cargo)
    {
        if (!$cargo->manual_funciones || !Storage::disk('s3')->exists($cargo->manual_funciones)) {
            abort(404, 'El archivo del manual no se encuentra en el almacenamiento.');
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $cargo->manual_funciones, 
            now()->addMinutes(20),
            [
                'ResponseContentType' => 'application/pdf',
                'ResponseContentDisposition' => 'inline; filename="manual_' . Str::slug($cargo->nombre_cargo) . '.pdf"'
            ]
        );

        return redirect($url);
    }
    
    /**
     * Exporta los datos actuales de la tabla a un archivo CSV.
     */
    public function exportCsv(Request $request)
    {
        // Importante: Usar GdoCargo para evitar el error de clase no encontrada
        $query = GdoCargo::with(['gdoArea', 'empleado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_cargo', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhereHas('gdoArea', fn($q) => $q->where('nombre', 'LIKE', "%{$search}%"))
                  ->orWhereHas('empleado', fn($q) => $q->where('nombre1', 'LIKE', "%{$search}%")
                                                     ->orWhere('apellido1', 'LIKE', "%{$search}%"));
            });
        }

        $cargos = $query->get();

        $callback = function() use ($cargos) {
            $file = fopen('php://output', 'w');
            // Añadir BOM para que Excel detecte UTF-8 y muestre tildes correctamente
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 

            fputcsv($file, ['ID', 'CARGO', 'AREA', 'COLABORADOR', 'CEDULA', 'ESTADO']);

            foreach ($cargos as $cargo) {
                fputcsv($file, [
                    $cargo->id,
                    $cargo->nombre_cargo,
                    $cargo->gdoArea->nombre ?? 'Sin área',
                    $cargo->empleado->nombre_completo ?? 'Vacante',
                    $cargo->empleado->cedula ?? 'N/A',
                    $cargo->estado ? 'Activo' : 'Inactivo'
                ]);
            }
            fclose($file);
        };

        $fileName = 'export_cargos_' . date('Y-m-d_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}