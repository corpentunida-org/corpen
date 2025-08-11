<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCargo;
use App\Models\Archivo\GdoArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GdoCargoController extends Controller
{
    public function index(Request $request)
    {
        $query = GdoCargo::with('gdoArea'); // relación con área

        if ($request->filled('search')) {
            $query->where('nombre_cargo', 'LIKE', '%' . $request->search . '%');
        }

        $cargos = $query->latest()->paginate(10);

        return view('archivo.cargo.index', compact('cargos'));
    }

    public function create()
    {
        $areas = GdoArea::orderBy('nombre')->get();
        return view('archivo.cargo.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_cargo'         => 'required|string|max:255',
            'salario_base'         => 'nullable|numeric|min:0',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            'manual_funciones'     => 'nullable|file|mimes:pdf|max:2048',
            'GDO_area_id'          => 'nullable|exists:gdo_area,id',
            'empleado_cedula'      => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);

        $data = $request->except('manual_funciones');

        if ($request->hasFile('manual_funciones')) {
            $data['manual_funciones'] = $request->file('manual_funciones')->store('gestion/cargos');
        }

        GdoCargo::create($data);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(GdoCargo $cargo)
    {
        $cargo->load('gdoArea');
        return view('archivo.cargo.show', compact('cargo'));
    }

    public function edit(GdoCargo $cargo)
    {
        $areas = GdoArea::orderBy('nombre')->get();
        return view('archivo.cargo.edit', compact('cargo', 'areas'));
    }

    public function update(Request $request, GdoCargo $cargo)
    {
        $request->validate([
            'nombre_cargo'         => 'required|string|max:255',
            'salario_base'         => 'nullable|numeric',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            'manual_funciones'     => 'nullable|file|mimes:pdf|max:2048',
            'GDO_area_id'          => 'nullable|exists:gdo_area,id',
            'empleado_cedula'      => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);

        $data = $request->except('manual_funciones');

        if ($request->hasFile('manual_funciones')) {
            if ($cargo->manual_funciones) {
                Storage::delete($cargo->manual_funciones);
            }
            $data['manual_funciones'] = $request->file('manual_funciones')->store('gestion/cargos');
        }

        $cargo->update($data);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo actualizado.');
    }

    public function destroy(GdoCargo $cargo)
    {
        if ($cargo->manual_funciones) {
            Storage::delete($cargo->manual_funciones);
        }

        $cargo->delete();
        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo eliminado.');
    }

    public function verManual(GdoCargo $cargo)
    {
        if (!$cargo->manual_funciones || !Storage::exists($cargo->manual_funciones)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::response($cargo->manual_funciones);
    }
}
