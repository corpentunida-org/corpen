<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\ComunicacionSalida;
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Plantilla;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // IMPORTANTE: Importamos la librería de PDF

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
        
        return view('correspondencia.comunicaciones_salida.create', compact('correspondencias', 'plantillas'));
    }

    /**
     * Almacenar nueva comunicación (CRUD puro).
     */
    public function store(Request $request)
    {
        // Validamos usando exactamente los valores que permite tu Base de Datos en el ENUM
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'nro_oficio_salida'  => 'required|string|unique:corr_comunicaciones_salida,nro_oficio_salida',
            'cuerpo_carta'       => 'required|string',
            'ruta_pdf'           => 'nullable|file|mimes:pdf|max:10240', // Por si suben un archivo manual (Máx 10MB)
            'estado_envio'       => 'required|in:Generado,Enviado por Email,Notificado Físicamente', 
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fecha_generacion'   => 'nullable|date',
        ]);

        // Asignación automática del usuario autenticado
        $data['fk_usuario'] = Auth::id();

        // Si el usuario subió un PDF firmado manualmente, lo guardamos. Si no, queda vacío.
        if ($request->hasFile('ruta_pdf')) {
            $data['ruta_pdf'] = $request->file('ruta_pdf')->store('comunicaciones');
        }

        // Guardamos los datos de la carta en la base de datos
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
     * Actualizar comunicación (CRUD puro).
     */
    public function update(Request $request, ComunicacionSalida $comunicacionSalida)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'nro_oficio_salida'  => 'required|string|unique:corr_comunicaciones_salida,nro_oficio_salida,'.$comunicacionSalida->id_respuesta.',id_respuesta',
            'cuerpo_carta'       => 'required|string',
            'estado_envio'       => 'required|in:Generado,Enviado por Email,Notificado Físicamente',
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fecha_generacion'   => 'nullable|date',
            'ruta_pdf'           => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Si suben un nuevo archivo PDF manualmente, borramos el anterior (si existía) y guardamos el nuevo
        if ($request->hasFile('ruta_pdf')) {
            if ($comunicacionSalida->ruta_pdf && Storage::exists($comunicacionSalida->ruta_pdf)) {
                Storage::delete($comunicacionSalida->ruta_pdf);
            }
            $data['ruta_pdf'] = $request->file('ruta_pdf')->store('comunicaciones');
        }

        // Actualizamos los datos en la base de datos
        $comunicacionSalida->update($data);

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación actualizada correctamente.');
    }

    /**
     * Eliminar comunicación y su archivo físico si lo hubiera.
     */
    public function destroy(ComunicacionSalida $comunicacionSalida)
    {
        if ($comunicacionSalida->ruta_pdf && Storage::exists($comunicacionSalida->ruta_pdf)) {
            Storage::delete($comunicacionSalida->ruta_pdf);
        }
        
        $comunicacionSalida->delete();

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación eliminada.');
    }

    /**
     * Generar y descargar el PDF "al vuelo" basado en los datos del CRUD.
     */
    public function descargarPdf($id)
    {
        // 1. Buscamos el registro en la BD y cargamos la relación del usuario (para la firma en el PDF)
        $comunicacion = ComunicacionSalida::with('usuario')->findOrFail($id);

        // 2. Si el registro tiene un PDF que fue subido manualmente, descargamos ese archivo
        if ($comunicacion->ruta_pdf && Storage::exists($comunicacion->ruta_pdf)) {
            return Storage::download($comunicacion->ruta_pdf);
        }

        // 3. Si no hay archivo subido, generamos el PDF en memoria "al vuelo" usando los datos guardados
        $pdf = Pdf::loadView('correspondencia.comunicaciones_salida.pdf', [
            'comunicacionSalida' => $comunicacion
        ]);

        // 4. Forzamos la descarga del PDF generado sin guardarlo en el servidor
        return $pdf->download('oficio_' . $comunicacion->nro_oficio_salida . '.pdf');
    }
}