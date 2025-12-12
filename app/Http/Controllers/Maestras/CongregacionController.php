<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\claseCongregacion;
use App\Models\Maestras\maeDistritos;
use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\MaeMunicipios;
use App\Models\Maestras\Congregacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Barryvdh\DomPDF\Facade\Pdf;

class CongregacionController extends Controller
{
    /**
     * Display a listing of the resource.
     * INICIO
     */
    public function index(Request $request)
    {
        // ... (toda la lógica de búsqueda se queda igual)
        $query = Congregacion::query()->with('claseCongregacion');
        $busqueda = trim($request->input('search'));

        if (!empty($busqueda)) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('municipio', 'LIKE', "%{$busqueda}%")
                    ->orWhere('pastor', 'LIKE', "%{$busqueda}%");
            });
        }

        // CAMBIO: Simplemente elimina .withQueryString() de esta línea
        $congregaciones = $query->orderBy('codigo', 'desc')->paginate(10);

        return view('maestras.congregaciones.index', compact('congregaciones'));
    }
    /**
     * Show the form for creating a new resource.
     * CREAR
     */
    public function create()
    {
        $claselist = claseCongregacion::all();
        $distritos = maeDistritos::all();
        $terceros = maeTerceros::all();
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
        if (Congregacion::where('codigo', $request->Codigo)->exists()) {
            return redirect()->back()->withInput()->with('error', 'El código ingresado ya está registrado.');
        }

        // Crear la congregación
        Congregacion::create([
            'codigo' => $request->Codigo,
            'nombre' => strtoupper($request->nombre),
            'pastor' => $request->pastor,
            'estado' => $request->estado,
            'clase' => $request->clase,
            'municipio' => $request->municipio,
            'direccion' => strtoupper($request->direccion),
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'distrito' => $request->distrito,
            'apertura' => $request->apertura,
            'cierre' => $request->cierre,
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación registrada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
     * TRAE PARA EDITAR
     */
    public function edit(Congregacion $congregacion)
    {
        $clases = claseCongregacion::all();
        $distritos = maeDistritos::all(); //get
        $pastores = maeTerceros::all();
        $municipios = MaeMunicipios::all();
        // La vista debe estar en la ruta correcta
        return view('maestras.congregaciones.edit', compact('congregacion', 'clases', 'distritos', 'pastores', 'municipios'));
    }
    /**
     * ACTUALIZA
     */
    public function update(Request $request, Congregacion $congregacion)
    {
        // El bloque de validación ha sido eliminado.

        // Se actualiza la congregación directamente con los datos del request.
        $congregacion->update([
            'nombre' => strtoupper($request->nombre), // Se mantiene la conversión a mayúsculas
            'pastor' => $request->pastor,
            'pastorAnterior' => $request->pastorAnterior,
            'estado' => $request->estado,
            'clase' => $request->clase,
            'municipio' => $request->municipio,
            'direccion' => strtoupper($request->direccion), // Se mantiene la conversión a mayúsculas
            'telefono' => $request->telefono,
            'celular' => $request->celular,
            'distrito' => $request->distrito,
            'apertura' => $request->apertura,
            'cierre' => $request->cierre,
            'observacion' => $request->observacion,
        ]);

        // Se redirige a la lista de congregaciones con un mensaje de éxito.
        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación actualizada exitosamente, y distrito del pastor sincronizado!');
    }

    /**
     * Remove the specified resource from storage.
     * ELIMINAR
     */
    public function destroy(Congregacion $congregacion)
    {
        try {
            // Elimina el modelo de la base de datos.
            $congregacion->delete();

            // Redirige de vuelta a la lista con un mensaje de éxito.
            return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación eliminada exitosamente!');
        } catch (\Exception $e) {
            // En caso de un error (por ejemplo, una restricción de clave foránea),
            // redirige con un mensaje de error.
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
    public function show($codigo)
    {
        $congregacion = Congregacion::with(['claseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTerceros'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        // Si el request trae ?pdf=1, generar el PDF con la MISMA vista show
        if (request()->has('pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('maestras.congregaciones.show', compact('congregacion'))->setPaper('a4', 'portrait');

            return $pdf->download('Informe_Congregacion_' . $congregacion->codigo . '.pdf');
        }

        // Si no es PDF, muestra la vista normalmente
        return view('maestras.congregaciones.show', compact('congregacion'));
    }
    public function generarPdf($codigo)
    {
        $congregacion = Congregacion::with(['claseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTerceros'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        $pdf = Pdf::loadView('maestras.congregacion.pdf', compact('congregacion'))->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Congregacion_' . $congregacion->codigo . '.pdf');
    }
}
