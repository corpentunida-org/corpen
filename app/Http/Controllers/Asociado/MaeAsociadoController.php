<?php

namespace App\Http\Controllers\Asociado;

use App\Http\Controllers\Controller;
use App\Models\Asociado\MaeAsociado;
use Illuminate\Http\Request;

class MaeAsociadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Conteo global para las tarjetas (siempre muestran el total general, sin importar el filtro)
        $totalExpedientes = MaeAsociado::count();
        $expedientesActivos = MaeAsociado::where('estado', 'Activo')->count();
        $expedientesInactivos = MaeAsociado::where('estado', 'Inactivo')->count();
        $digitalizadosEcm = MaeAsociado::where('cargado_ecm', true)->count();
        
        // Mejor forma de contar nulos o vacíos
        $pendientesArchivo = MaeAsociado::where(function($q) {
            $q->whereNull('radicado')->orWhere('radicado', '');
        })->count();

        // 2. Consulta base para la tabla
        $query = MaeAsociado::query();

        // 3. Filtro por Buscador Global (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cedula', 'LIKE', "%{$search}%")
                  ->orWhere('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('distrito_actual', 'LIKE', "%{$search}%")
                  ->orWhere('ciudad_distrito', 'LIKE', "%{$search}%");
            });
        }

        // 4. Filtro por Clic en Tarjetas (Filter)
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'activos':
                    $query->where('estado', 'Activo');
                    break;
                case 'inactivos':
                    $query->where('estado', 'Inactivo');
                    break;
                case 'ecm':
                    $query->where('cargado_ecm', true);
                    break;
                case 'pendientes':
                    $query->where(function($q) {
                        $q->whereNull('radicado')->orWhere('radicado', '');
                    });
                    break;
                // 'total' muestra todos por defecto, no requiere condicional
            }
        }

        // 5. Paginación (withQueryString preserva los filtros al cambiar de página)
        $asociados = $query->latest()->paginate(15)->withQueryString();
        
        return view('asociados.index', compact(
            'asociados', 
            'totalExpedientes', 
            'expedientesActivos', 
            'expedientesInactivos', 
            'digitalizadosEcm', 
            'pendientesArchivo'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('asociados.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación centralizada
        $validated = $request->validate($this->getValidationRules());

        // Manejo nativo y seguro de campos booleanos en Laravel
        $validated['escaneado']        = $request->boolean('escaneado');
        $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
        $validated['validado_archivo'] = $request->boolean('validado_archivo');

        // Asignación de estado por defecto si no viene en el request
        $validated['estado'] = $request->input('estado', 'Activo');

        MaeAsociado::create($validated);

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Expediente del asociado creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaeAsociado $maeAsociado)
    {
        return view('asociados.show', compact('maeAsociado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaeAsociado $maeAsociado)
    {
        return view('asociados.edit', compact('maeAsociado'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaeAsociado $maeAsociado)
    {
        // Validación centralizada ignorando el ID actual para la cédula
        $validated = $request->validate($this->getValidationRules($maeAsociado->id));

        // Manejo nativo y seguro de campos booleanos
        $validated['escaneado']        = $request->boolean('escaneado');
        $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
        $validated['validado_archivo'] = $request->boolean('validado_archivo');

        $maeAsociado->update($validated);

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Expediente del asociado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaeAsociado $maeAsociado)
    {
        // En sistemas ECM de alto nivel, se recomienda SoftDeletes. 
        // Si usas eliminación física, se mantiene el delete().
        $maeAsociado->delete();

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Asociado eliminado del sistema correctamente.');
    }

    /**
     * Buscar un asociado por cédula (Ejemplo para peticiones AJAX/API)
     */
    public function buscarCedula($cedula)
    {
        $asociado = MaeAsociado::where('cedula', $cedula)->first();

        if (!$asociado) {
            return response()->json(['status' => 'error', 'message' => 'No se encontró el asociado en el directorio.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $asociado->id,
                'nombre_completo' => $asociado->nombre_completo,
                'celular'         => $asociado->celular_pastor,
                'distrito'        => $asociado->distrito_actual,
                'estado'          => $asociado->estado
            ]
        ]);
    }

    /**
     * Método privado para centralizar las reglas de validación de los 48 campos.
     * @param int|null $id ID del registro a excluir en reglas unique.
     * @return array
     */
    private function getValidationRules($id = null)
    {
        return [
            // Identidad y Demografía
            'cedula'                    => 'required|string|max:15|unique:mae_asociados,cedula,' . $id,
            'nombre1'                   => 'required|string|max:100',
            'nombre2'                   => 'nullable|string|max:100',
            'apellido1'                 => 'required|string|max:100',
            'apellido2'                 => 'nullable|string|max:100',
            'fecha_nacimiento'          => 'nullable|date',
            'lugar_expedicion_cedula'   => 'nullable|string|max:50',
            'fecha_expedicion'          => 'nullable|date',
            'estado_civil'              => 'nullable|string|max:20',

            // Contacto
            'correo_pastor'             => 'nullable|email|max:100',
            'celular_pastor'            => 'nullable|string|max:15',
            'whatsapp'                  => 'nullable|string|max:15',

            // Información Ministerial
            'fecha_afiliacion'          => 'nullable|date',
            'distrito_actual'           => 'nullable|string|max:50',
            'ciudad_distrito'           => 'nullable|string|max:50',
            'direccion_distrito'        => 'nullable|string|max:100',
            'estado_pastor'             => 'nullable|string|max:20',
            'especificacion'            => 'nullable|string|max:50',
            'licencia'                  => 'nullable|string|max:30',
            'pais'                      => 'nullable|string|max:30',
            'iglesia_actual'            => 'nullable|string|max:100',

            // Familia (Cónyuge)
            'cedula_esposa'             => 'nullable|string|max:15',
            'nombre_esposa'             => 'nullable|string|max:100',
            'correo_esposa'             => 'nullable|email|max:100',
            'celular_esposa'            => 'nullable|string|max:15',

            // Soportes Documentales
            'doc_formulario_afiliacion' => 'nullable|string|max:20',
            'doc_autorizacion_datos'    => 'nullable|string|max:20',
            'doc_cedula_pastor'         => 'nullable|string|max:20',
            'doc_cedula_esposa'         => 'nullable|string|max:20',
            'doc_licencia_pastoral'     => 'nullable|string|max:20',
            'doc_registro_matrimonio'   => 'nullable|string|max:20',
            'doc_id_hijos'              => 'nullable|string|max:20',

            // Gestión de Archivo ECM y Físico
            'ubicacion_ecm_link'        => 'nullable|url|max:255',
            'radicado'                  => 'nullable|string|max:20',
            'ubicacion_carpeta'         => 'nullable|string|max:30',
            'numero_caja'               => 'nullable|string|max:20',
            'cantidad_folios'           => 'nullable|integer|min:0',
            'fecha_ingreso_archivo'     => 'nullable|date',
            'estado_conservacion'       => 'nullable|string|max:30',
            'custodia_actual'           => 'nullable|string|max:50',
            'observaciones_archivo'     => 'nullable|string',

            // Metadatos Adicionales
            'observaciones_generales'   => 'nullable|string',
            'estado'                    => 'nullable|string|max:20',
        ];
    }
}