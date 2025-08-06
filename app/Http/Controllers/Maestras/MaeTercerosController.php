<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\MaeTipo;
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
        $tipos = MaeTipo::all();

        return view('maestras.terceros.edit', compact('tercero','tipos'));
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
        $tipos = MaeTipo::all();

        // Aquí retornas la vista para crear un nuevo tercero
        return view('maestras.terceros.create', compact('tipos'));
    }

// En tu TercerosController.php

public function store(Request $request)
{
    // Opcional pero muy recomendado: Añade validación
    $request->validate([
        'cod_ter' => 'required|string|max:20|unique:MaeTerceros,cod_ter',
        'nombre' => 'required|string|max:255',
        'tip_prv' => 'required', // Asegura que se seleccionó un tipo
        // ... otras reglas de validación para teléfono, email, etc.
    ]);

    maeTerceros::create([
        'cod_ter' => $request->cod_ter,
        'nom_ter' => $request->nombre,
        // ===== CORRECCIÓN AQUÍ =====
        'tip_prv' => $request->tip_prv, // Debe ser 'tip_prv' para que coincida con el name del select
        // ===========================
        'cel'     => $request->telefono,
        'dir'     => $request->direccion,
        'ciudad'  => $request->ciudad,
        'dpto'    => $request->departamento,
        'email'   => $request->email, // No olvides guardar el email también
    ]);

    return redirect()
        ->route('maestras.terceros.index')
        ->with('success', 'Tercero creado correctamente.');
}

    public function destroy(maeTerceros $tercero) {
        $tercero->delete();
        return redirect()->route('maestras.terceros.index')->with('success', 'Tercero eliminado.');
    }

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