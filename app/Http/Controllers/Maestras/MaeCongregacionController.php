<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeClaseCongregacion;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeTerceros;
use App\Models\Maestras\MaeMunicipios;
use App\Models\Maestras\MaeCongregacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class MaeCongregacionController extends Controller
{
    /**
     * Display a listing of the resource.
     * INICIO
     */
    /**
     * Antes solo había un buscador genérico que probaba el término contra
     * MaeCongregaciones.pastor — pero esa columna guarda la CÉDULA del pastor (texto legado),
     * no su nombre, así que buscar "Pérez" nunca encontraba nada aunque Pérez fuera el titular
     * real. Se agregan 3 filtros propios (nombre, código, pastor) y el de pastor busca por
     * cédula Y por nombre real, cruzando contra MaeTerceros vía la relación maeTercero()
     * (congrega = codigo), que es la fuente de verdad actual del titular.
     */
    public function index(Request $request)
    {
        $query = MaeCongregacion::query()->with(['maeClaseCongregacion', 'maeTercero']);

        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', '%' . $request->input('nombre') . '%');
        }

        if ($request->filled('codigo')) {
            $query->where('codigo', 'LIKE', '%' . $request->input('codigo') . '%');
        }

        if ($request->filled('pastor')) {
            $pastor = $request->input('pastor');

            // orWhereHas() aquí generaba un subquery DEPENDIENTE (una vuelta a MaeTerceros por
            // CADA congregación, ~6.559 veces) que MySQL no resuelve con el índice de
            // 'congrega' cuando se combina con el LIKE — se confirmó con EXPLAIN: type=ALL,
            // ~160 millones de comparaciones, la pantalla se colgaba varios minutos. En vez de
            // eso, se busca en MaeTerceros UNA sola vez (sin correlación) y se filtra
            // MaeCongregaciones por los códigos resultantes con un IN, que sí usa el índice.
            $codigosConPastorCoincidente = MaeTerceros::where('nom_ter', 'LIKE', "%{$pastor}%")
                ->orWhere('cod_ter', 'LIKE', "%{$pastor}%")
                ->pluck('congrega')
                ->filter()
                ->unique();

            $query->where(function ($q) use ($pastor, $codigosConPastorCoincidente) {
                $q->where('pastor', 'LIKE', "%{$pastor}%")
                    ->orWhereIn('codigo', $codigosConPastorCoincidente);
            });
        }

        $congregaciones = $query->orderBy('codigo', 'desc')->paginate(10)->withQueryString();

        return view('maestras.congregaciones.index', compact('congregaciones'));
    }

    /**
     * Show the form for creating a new resource.
     * CREAR
     */
    public function create()
    {
        $claselist = MaeClaseCongregacion::all();
        $distritos = MaeDistritos::all();
        $terceros = MaeTerceros::all();
        $municipios = MaeMunicipios::all();

        return view('maestras.congregaciones.create', compact('claselist', 'distritos', 'terceros', 'municipios'));
    }

    /**
     * Store a newly created resource in storage.
     * ALMACENA PARA CREAR
     */
    public function store(Request $request)
    {
        // Verificar si ya existe una congregación con ese código
        if (MaeCongregacion::where('codigo', $request->Codigo)->exists()) {
            return redirect()->back()->withInput()->with('error', 'El código ingresado ya está registrado.');
        }

        if ($error = $this->errorPastorYaAsignado($request->pastor)) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        // Crear la congregación
        $congregacion = MaeCongregacion::create([
            'codigo' => $request->Codigo,
            'nombre' => strtoupper($request->nombre),
            'pastor' => $request->pastor,
            'estado' => $request->estado,
            'clase' => $request->clase,
            'municipio' => $request->municipio,
            'municipio_exterior_detalle' => $this->municipioExteriorDetalle($request),
            'direccion' => strtoupper($request->direccion),
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'distrito' => $request->distrito,
            'apertura' => $request->apertura,
            'cierre' => $request->cierre,
            'observacion' => $request->observacion,
        ]);

        $this->sincronizarPastorConTercero($congregacion);

        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación registrada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
     * TRAE PARA EDITAR
     */
    public function edit(MaeCongregacion $congregacion)
    {
        $clases = MaeClaseCongregacion::all();
        $distritos = MaeDistritos::all();
        $pastores = MaeTerceros::all();
        $municipios = MaeMunicipios::all();

        return view('maestras.congregaciones.edit', compact('congregacion', 'clases', 'distritos', 'pastores', 'municipios'));
    }

    /**
     * ACTUALIZA
     */
    public function update(Request $request, MaeCongregacion $congregacion)
    {
        if ($error = $this->errorPastorYaAsignado($request->pastor, $congregacion->codigo)) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        // 'pastorAnterior' ya NO se recibe del formulario (antes era un campo libre editable a
        // mano, sin relación real con quién fue el pastor antes — cualquiera podía escribir
        // cualquier cédula ahí). Ahora se calcula solo: si el titular ('pastor') realmente
        // cambia en este guardado, el que se está reemplazando pasa a 'pastorAnterior'. Si el
        // titular no cambia, 'pastorAnterior' se deja tal como está.
        $pastorAnterior = $congregacion->pastorAnterior;
        $tituarActual = trim((string) $congregacion->pastor);
        $tituarNuevo = trim((string) $request->pastor);
        if ($tituarActual !== '' && $tituarActual !== $tituarNuevo) {
            $pastorAnterior = $tituarActual;
        }

        // Se actualiza la congregación directamente con los datos del request.
        $congregacion->update([
            'nombre' => strtoupper($request->nombre),
            'pastor' => $request->pastor,
            'pastorAnterior' => $pastorAnterior,
            'estado' => $request->estado,
            'clase' => $request->clase,
            'municipio' => $request->municipio,
            'municipio_exterior_detalle' => $this->municipioExteriorDetalle($request),
            'direccion' => strtoupper($request->direccion),
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'distrito' => $request->distrito,
            'apertura' => $request->apertura,
            'cierre' => $request->cierre,
            'observacion' => $request->observacion,
        ]);

        $this->sincronizarPastorConTercero($congregacion);

        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación actualizada exitosamente!');
    }

    /**
     * Un pastor solo puede estar a cargo de una congregación a la vez. Se valida antes de
     * guardar en vez de dejar que ocurra y quedar con el mismo pastor "activo" en dos partes.
     */
    private function errorPastorYaAsignado(?string $pastor, ?string $codigoActual = null): ?string
    {
        if ($pastor === null || $pastor === '') {
            return null;
        }

        $otra = MaeCongregacion::where('pastor', $pastor)
            ->when($codigoActual, fn ($q) => $q->where('codigo', '!=', $codigoActual))
            ->first();

        if (!$otra) {
            return null;
        }

        return "Este pastor ya está a cargo de la congregación {$otra->codigo} - {$otra->nombre}. Retíralo de ahí primero (o usa esa congregación) antes de asignarlo aquí.";
    }

    /**
     * El pastor asignado a la congregación queda enlazado en su propio registro de tercero
     * (congrega + cod_dist), la única forma en que esos dos campos se actualizan — ver
     * MaeTercerosController::store()/update(), donde quedaron bloqueados.
     */
    private function sincronizarPastorConTercero(MaeCongregacion $congregacion): void
    {
        if ($congregacion->pastor === null || $congregacion->pastor === '') {
            return;
        }

        MaeTerceros::where('cod_ter', $congregacion->pastor)->update([
            'congrega' => $congregacion->codigo,
            'cod_dist' => $congregacion->distrito,
        ]);
    }

    /**
     * El detalle libre (ciudad/país) solo tiene sentido cuando se elige el municipio
     * "sentinela" de Otro/Exterior; si se elige un municipio real, se limpia para no dejar
     * texto viejo colgado de una elección anterior.
     */
    private function municipioExteriorDetalle(Request $request): ?string
    {
        if ((int) $request->municipio !== MaeCongregacion::MUNICIPIO_EXTERIOR_ID) {
            return null;
        }

        return $request->municipio_exterior_detalle ? strtoupper($request->municipio_exterior_detalle) : null;
    }

    /**
     * Remove the specified resource from storage.
     * ELIMINAR
     */
    public function destroy(MaeCongregacion $congregacion)
    {
        try {
            // Elimina el modelo de la base de datos.
            $congregacion->delete();

            return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación eliminada exitosamente!');
        } catch (\Exception $e) {
            // En caso de un error (por ejemplo, una restricción de clave foránea)
            return redirect()->route('maestras.congregacion.index')->with('error', 'No se pudo eliminar la congregación. Es posible que esté asociada a otros registros.');
        }
    }

    /**
     * PARA TRAER EL NOMBRE DEL TERCERO
     */
    public function buscarPastor(Request $request)
    {
        $cedula = $request->get('cedula');
        $pastor = MaeTerceros::where('cod_ter', $cedula)->first();

        if ($pastor) {
            return response()->json(['nombre' => $pastor->nom_ter]);
        } else {
            return response()->json(['nombre' => 'No encontrado'], 404);
        }
    }

    /**
     * MOSTRAR CONGREGACIÓN Y GENERAR PDF (MISMA VISTA)
     */
    public function show($codigo)
    {
        $congregacion = MaeCongregacion::with(['maeClaseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTercero'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        // Si el request trae ?pdf=1, generar el PDF con la MISMA vista show usando Pdf::
        if (request()->has('pdf')) {
            $pdf = Pdf::loadView('maestras.congregaciones.show', compact('congregacion'))->setPaper('a4', 'portrait');

            return $pdf->download('Informe_Congregacion_' . $congregacion->codigo . '.pdf');
        }

        // Si no es PDF, muestra la vista normalmente
        return view('maestras.congregaciones.show', compact('congregacion'));
    }

    /**
     * GENERAR PDF INDEPENDIENTE
     */
    public function generarPdf($codigo)
    {
        $congregacion = MaeCongregacion::with(['maeClaseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTercero'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        // Usando Pdf:: en lugar de la ruta completa de Facade
        $pdf = Pdf::loadView('maestras.congregacion.pdf', compact('congregacion'))->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Congregacion_' . $congregacion->codigo . '.pdf');
    }
}
