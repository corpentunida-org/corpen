<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntChannel;
use Illuminate\Http\Request;

class IntChannelController extends Controller
{
    /**
     * Mostrar lista de canales
     */
    public function index(Request $request)
    {
        $channels = IntChannel::withCount('interactions')
            ->when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('interactions.channels.index', compact('channels'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('interactions.channels.create');
    }

    /**
     * Guardar un nuevo canal
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $channel = IntChannel::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.channels.index')
            ->with('success', 'Canal creado exitosamente');
    }

    /**
     * Mostrar un canal específico (para API u otros usos)
     */
    public function show($id)
    {
        $channel = IntChannel::withCount('interactions')->findOrFail($id);
        return view('interactions.channels.show', compact('channel'));
    }
    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $channel = IntChannel::findOrFail($id);
        return view('interactions.channels.edit', compact('channel'));
    }

    /**
     * Actualizar un canal
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $channel = IntChannel::findOrFail($id);
        $channel->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.channels.index')
            ->with('success', 'Canal actualizado correctamente');
    }

    /**
     * Eliminar un canal
     */
    public function destroy($id)
    {
        $channel = IntChannel::withCount('interactions')->findOrFail($id);

        // UX: Validación de seguridad
        if ($channel->interactions_count > 0) {
            return redirect()->back()->with('error', 'El canal tiene registros vinculados y no puede eliminarse.');
        }

        $channel->delete();
        return redirect()->route('interactions.channels.index')->with('success', 'Eliminado correctamente.');
    }
}
