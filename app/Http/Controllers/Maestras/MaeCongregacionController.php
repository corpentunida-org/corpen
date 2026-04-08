<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeClaseCongregacion;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\maeTerceros;
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
    public function index(Request $request)
    {
        $query = MaeCongregacion::query()->with('maeClaseCongregacion');
        $busqueda = trim($request->input('search'));

        if (!empty($busqueda)) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                  ->orWhere('municipio', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pastor', 'LIKE', "%{$busqueda}%");
            });
        }

        $congregaciones = $query->orderBy('codigo', 'desc')->paginate(10);

        return view('maestras.congregaciones.index', compact('congregaciones'));
    }

    /**
     * Show the form for creating a new resource.
     * CREAR
     */
    public function create()
    {
        $claselist  = MaeClaseCongregacion::all();
        $distritos  = MaeDistritos::all();
        $terceros   = maeTerceros::all();
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

        // Crear la congregación
        MaeCongregacion::create([
            'codigo'      => $request->Codigo,
            'nombre'      => strtoupper($request->nombre),
            'pastor'      => $request->pastor,
            'estado'      => $request->estado,
            'clase'       => $request->clase,
            'municipio'   => $request->municipio,
            'direccion'   => strtoupper($request->direccion),
            'telefono'    => $request->telefono,
            'celular'     => $request->celular,
            'distrito'    => $request->distrito,
            'apertura'    => $request->apertura,
            'cierre'      => $request->cierre,
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación registrada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
     * TRAE PARA EDITAR
     */
    public function edit(MaeCongregacion $congregacion)
    {
        $clases     = MaeClaseCongregacion::all();
        $distritos  = MaeDistritos::all(); 
        $pastores   = maeTerceros::all();
        $municipios = MaeMunicipios::all();
        
        return view('maestras.congregaciones.edit', compact('congregacion', 'clases', 'distritos', 'pastores', 'municipios'));
    }

    /**
     * ACTUALIZA
     */
    public function update(Request $request, MaeCongregacion $congregacion)
    {
        // Se actualiza la congregación directamente con los datos del request.
        $congregacion->update([
            'nombre'         => strtoupper($request->nombre), 
            'pastor'         => $request->pastor,
            'pastorAnterior' => $request->pastorAnterior,
            'estado'         => $request->estado,
            'clase'          => $request->clase,
            'municipio'      => $request->municipio,
            'direccion'      => strtoupper($request->direccion), 
            'telefono'       => $request->telefono,
            'celular'        => $request->celular,
            'distrito'       => $request->distrito,
            'apertura'       => $request->apertura,
            'cierre'         => $request->cierre,
            'observacion'    => $request->observacion,
        ]);

        return redirect()->route('maestras.congregacion.index')->with('success', '¡Congregación actualizada exitosamente!');
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
        $pastor = maeTerceros::where('cod_ter', $cedula)->first();

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
        $congregacion = MaeCongregacion::with(['maeClaseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTerceros'])
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
        $congregacion = MaeCongregacion::with(['maeClaseCongregacion', 'maeDistritos', 'maeMunicipios', 'maeTerceros'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        // Usando Pdf:: en lugar de la ruta completa de Facade
        $pdf = Pdf::loadView('maestras.congregacion.pdf', compact('congregacion'))->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Congregacion_' . $congregacion->codigo . '.pdf');
    }
}