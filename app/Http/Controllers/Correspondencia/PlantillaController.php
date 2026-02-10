<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Plantilla;

class PlantillaController extends Controller
{
    public function index()
    {
        $plantillas = Plantilla::paginate(15);
        return view('correspondencia.plantillas.index', compact('plantillas'));
    }

    public function create()
    {
        return view('correspondencia.plantillas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_plantilla' => 'required|string|max:150',
            'html_base' => 'required|string',
        ]);

        Plantilla::create($data);

        return redirect()->route('correspondencia.plantillas.index')->with('success','Plantilla creada');
    }

    public function show(Plantilla $plantilla)
    {
        return view('correspondencia.plantillas.show', compact('plantilla'));
    }

    public function edit(Plantilla $plantilla)
    {
        return view('correspondencia.plantillas.edit', compact('plantilla'));
    }

    public function update(Request $request, Plantilla $plantilla)
    {
        $data = $request->validate([
            'nombre_plantilla' => 'required|string|max:150',
            'html_base' => 'required|string',
        ]);

        $plantilla->update($data);

        return redirect()->route('correspondencia.plantillas.index')->with('success','Plantilla actualizada');
    }

    public function destroy(Plantilla $plantilla)
    {
        $plantilla->delete();
        return redirect()->route('correspondencia.plantillas.index')->with('success','Plantilla eliminada');
    }
}
