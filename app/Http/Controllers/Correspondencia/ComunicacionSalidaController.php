<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\ComunicacionSalida;
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Plantilla;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ComunicacionSalidaController extends Controller
{
    /**
     * Listado de comunicaciones apuntando a la carpeta correcta.
     */
    public function index()
    {
        $comunicaciones = ComunicacionSalida::with(['correspondencia', 'plantilla', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('correspondencia.comunicaciones_salida.index', compact('comunicaciones'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $correspondencias = Correspondencia::all();
        $plantillas = Plantilla::all();
        // Nota: El usuario se toma de auth() en el store, no es necesario pasarlo a la vista 
        // a menos que quieras permitir cambiar el firmante.
        return view('correspondencia.comunicaciones_salida.create', compact('correspondencias', 'plantillas'));
    }

    /**
     * Almacenar nueva comunicación.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'nro_oficio_salida'  => 'required|string|unique:corr_comunicaciones_salida,nro_oficio_salida',
            'cuerpo_carta'       => 'required|string',
            'ruta_pdf'           => 'nullable|file|mimes:pdf|max:10240', // Máx 10MB
            'estado_envio'       => 'required|string',
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fecha_generacion'   => 'nullable|date',
        ]);

        // Asignación automática del usuario autenticado
        $data['fk_usuario'] = Auth::id();

        if ($request->hasFile('ruta_pdf')) {
            $data['ruta_pdf'] = $request->file('ruta_pdf')->store('comunicaciones');
        }

        ComunicacionSalida::create($data);

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación de salida registrada correctamente.');
    }

    /**
     * Mostrar detalle.
     */
    public function show(ComunicacionSalida $comunicacionSalida)
    {
        return view('correspondencia.comunicaciones_salida.show', compact('comunicacionSalida'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(ComunicacionSalida $comunicacionSalida)
    {
        $correspondencias = Correspondencia::all();
        $plantillas = Plantilla::all();
        
        return view('correspondencia.comunicaciones_salida.edit', compact('comunicacionSalida', 'correspondencias', 'plantillas'));
    }

    /**
     * Actualizar comunicación.
     */
    public function update(Request $request, ComunicacionSalida $comunicacionSalida)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'nro_oficio_salida'  => 'required|string|unique:corr_comunicaciones_salida,nro_oficio_salida,'.$comunicacionSalida->id_respuesta.',id_respuesta',
            'cuerpo_carta'       => 'required|string',
            'estado_envio'       => 'required|string',
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fecha_generacion'   => 'nullable|date',
        ]);

        if ($request->hasFile('ruta_pdf')) {
            // Eliminar archivo anterior si existe
            if ($comunicacionSalida->ruta_pdf) {
                Storage::delete($comunicacionSalida->ruta_pdf);
            }
            $data['ruta_pdf'] = $request->file('ruta_pdf')->store('comunicaciones');
        }

        $comunicacionSalida->update($data);

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación actualizada correctamente.');
    }

    /**
     * Eliminar comunicación y su archivo físico.
     */
    public function destroy(ComunicacionSalida $comunicacionSalida)
    {
        if ($comunicacionSalida->ruta_pdf) {
            Storage::delete($comunicacionSalida->ruta_pdf);
        }
        
        $comunicacionSalida->delete();

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación eliminada.');
    }

    /**
     * Descarga de archivos PDF.
     */
    /*  */
}