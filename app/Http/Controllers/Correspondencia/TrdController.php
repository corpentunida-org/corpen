<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Trd;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\User;

class TrdController extends Controller
{
    public function index()
    {
        // Cargamos las relaciones para evitar el problema de consultas N+1
        $trds = Trd::with(['usuario', 'flujo'])->paginate(15);
        return view('correspondencia.trds.index', compact('trds'));
    }

    public function create()
    {
        $flujos = FlujoDeTrabajo::all();
        // El usuario se toma de la sesión, no es necesario pasar todos los usuarios
        return view('correspondencia.trds.create', compact('flujos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'serie_documental'  => 'required|string|max:100',
            'tiempo_gestion'    => 'required|integer|min:0',
            'tiempo_central'    => 'required|integer|min:0',
            'disposicion_final' => 'required|in:conservar,eliminar',
            'usuario_id'        => 'required|exists:users,id',
            'fk_flujo'          => 'required|exists:corr_flujo_de_trabajo,id',
        ]);

        Trd::create($data);

        return redirect()->route('correspondencia.trds.index')
            ->with('success', 'Serie Documental (TRD) creada correctamente');
    }

    public function show(Trd $trd)
    {
        // Cargar relación para la vista de detalle
        $trd->load(['usuario', 'flujo']);
        return view('correspondencia.trds.show', compact('trd'));
    }

    public function edit(Trd $trd)
    {
        $flujos = FlujoDeTrabajo::all();
        return view('correspondencia.trds.edit', compact('trd', 'flujos'));
    }

    public function update(Request $request, Trd $trd)
    {
        $data = $request->validate([
            'serie_documental'  => 'required|string|max:100',
            'tiempo_gestion'    => 'required|integer|min:0',
            'tiempo_central'    => 'required|integer|min:0',
            'disposicion_final' => 'required|in:conservar,eliminar',
            'fk_flujo'          => 'required|exists:corr_flujo_de_trabajo,id',
        ]);

        // Mantenemos el usuario_id original o lo actualizamos según tu regla de negocio
        $trd->update($data);

        return redirect()->route('correspondencia.trds.index')
            ->with('success', 'TRD actualizada correctamente');
    }
    /* 
    public function destroy(Trd $trd)
    {
        try {
            $trd->delete();
            return redirect()->route('correspondencia.trds.index')
                ->with('success', 'TRD eliminada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('correspondencia.trds.index')
                ->with('error', 'No se puede eliminar la TRD porque está siendo utilizada en registros de correspondencia.');
        }
    }
     */
}