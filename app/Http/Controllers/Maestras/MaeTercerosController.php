<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;



class MaeTercerosController extends Controller
{
    /**
     * Muestra una lista de los terceros.
     */
    public function index(Request $request)
    {
        // Opcional: filtro por nombre
        $search = $request->input('search');

        $terceros = maeTerceros::query()
            ->when($search, function ($query, $search) {
                $query->where('nom_ter', 'like', "%{$search}%")
                      ->orWhere('razon_soc', 'like', "%{$search}%")
                      ->orWhere('cod_ter', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10); // Ajusta la paginación si deseas más o menos

        return view('maestras.terceros.index', compact('terceros', 'search'));
    }

    public function edit(maeTerceros $tercero)
    {
        return view('maestras.terceros.edit', compact('tercero'));
    }

    public function update(Request $request, MaeTerceros $tercero)
    {
        // dd($request->all());

        $tercero->update($request->all());

        return redirect()->route('maestras.terceros.index')
                        ->with('success', 'Tercero actualizado correctamente.');
    }

    public function create()
    {
        // Aquí retornas la vista para crear un nuevo tercero
        return view('maestras.terceros.create');
    }

    public function store(Request $request)
    {
        maeTerceros::create([
            'cod_ter' => $request->cod_ter,
            'nom_ter' => $request->nombre,   // Aquí mapeas nombre => nom_ter
            'cel' => $request->telefono,    // Mapeas telefono => tel1
            'dir' => $request->direccion,    // Mapeas direccion => dir
            'ciudad' => $request->ciudad,
            'dpto' => $request->departamento,
            // y así con los demás si quieres
        ]);

        return redirect()->route('maestras.terceros.index')->with('success', 'Tercero creado correctamente.');
    }

    public function destroy(maeTerceros $tercero) {
        $tercero->delete();
        return redirect()->route('maestras.terceros.index')->with('success', 'Tercero eliminado.');
    }

    // show() debe coincidir con el parámetro:
//    public function show(Request $request, maeTerceros $tercero)
//    {
//        // Si se pasa ?pdf=1, podrías usar una librería tipo barryvdh/laravel-dompdf para exportar.
//        if ($request->has('pdf')) {
//            $pdf = \PDF::loadView('maestras.terceros.show', compact('tercero'));
//            return $pdf->stream("Tercero_{$tercero->cod_ter}.pdf");
//       }

//        return view('maestras.terceros.show', compact('tercero'));
//    }





public function show(maeTerceros $tercero)
{
    if (request()->has('pdf')) {
        $pdf = Pdf::loadView('maestras.terceros.show', compact('tercero'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
    }

    return view('maestras.terceros.show', compact('tercero'));
}

public function generarPdf($cod_ter)
{
    $tercero = maeTerceros::with([
        'congregaciones'
    ])->where('cod_ter', $cod_ter)->firstOrFail();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('maestras.terceros.pdf', compact('tercero'))
                ->setPaper('a4', 'portrait');

    return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
}


}