<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GoogleDriveService;
use Exception;
use Illuminate\Support\Facades\Log;

class InteractionController extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    /**
     * Display a listing of the resource.
     * Muestra una lista de interacciones (para la redirección de éxito).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $interactions = Interaction::all(); // O paginar, etc.
        return view('interactions.index', compact('interactions'));
    }

    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear una nueva interacción.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Si todavía no tienes modelos Client o Agent, puedes enviar arrays vacíos
        $clients = []; 
        $agents = [];

        return view('interactions.create', compact('clients', 'agents'));
    }

    /**
     * Store a newly created resource in storage (for Web requests).
     * Almacena una nueva interacción, con redirecciones y manejo de errores para Blade.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interaction_date'    => 'required|date',
            'interaction_channel' => 'required|string|max:255',
            'interaction_type'    => 'required|string|max:255',
            'duration'            => 'nullable|integer',
            'outcome'             => 'required|string|max:255',
            'notes'               => 'nullable|string',
            'parent_interaction_id' => 'nullable|integer|exists:interactions,id',
            'next_action_date'    => 'nullable|date',
            'next_action_type'    => 'nullable|string|max:255',
            'next_action_notes'   => 'nullable|string',
            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:jpeg,png,pdf|max:10240',
            'interaction_url'     => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        $uploadedUrls = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();

                try {
                    // Usa getRealPath en lugar de getPathname
                    $driveUrl = $this->googleDriveService->uploadFile(
                        $file->getRealPath(),
                        $fileName,
                        $mimeType
                    );

                    if ($driveUrl) {
                        $uploadedUrls[] = $driveUrl;
                    } else {
                        Log::warning('No se pudo subir el archivo ' . $fileName . ' a Google Drive.');
                    }
                } catch (Exception $e) {
                    Log::error('Error al subir archivo a Google Drive: ' . $e->getMessage());
                }
            }
        }

        try {
            $data = $request->except(['attachments']);
            // Convertimos array a JSON para guardar en la DB
            $data['attachment_urls'] = json_encode($uploadedUrls);

            Interaction::create($data);

            return redirect()->route('interactions.index')
                            ->with('success', 'Interacción creada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al crear la interacción: ' . $e->getMessage());
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al crear la interacción: ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Interaction  $interaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Interaction $interaction)
    {
        return view('interactions.edit', compact('interaction'));
    }

    /**
     * Update the specified resource in storage (for Web requests).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Interaction  $interaction
     * @return \Illuminate\Http\Response
     */
    public function updateWeb(Request $request, Interaction $interaction)
    {
        $validator = Validator::make($request->all(), [
            'interaction_date'    => 'sometimes|required|date',
            'interaction_channel' => 'sometimes|required|string|max:255',
            'interaction_type'    => 'sometimes|required|string|max:255',
            'duration'            => 'nullable|integer',
            'outcome'             => 'sometimes|required|string|max:255',
            'notes'               => 'nullable|string',
            'parent_interaction_id' => 'nullable|integer|exists:interactions,id',
            'next_action_date'    => 'nullable|date',
            'next_action_type'    => 'nullable|string|max:255',
            'next_action_notes'   => 'nullable|string',
            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:jpeg,png,pdf|max:10240',
            'existing_attachment_urls' => 'nullable|array',
            'existing_attachment_urls.*' => 'url',
            'interaction_url'     => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        $updatedUrls = $request->input('existing_attachment_urls', []);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $mimeType = $file->getMimeType();

                try {
                    $driveUrl = $this->googleDriveService->uploadFile($file->getPathname(), $fileName, $mimeType);
                    if ($driveUrl) {
                        $updatedUrls[] = $driveUrl;
                    } else {
                        Log::warning('No se pudo subir el archivo ' . $fileName . ' durante la actualización.');
                        session()->flash('warning', 'Algunos archivos nuevos no se pudieron subir.');
                    }
                } catch (Exception $e) {
                    Log::error('Error al subir archivo a Google Drive durante actualización: ' . $e->getMessage());
                    session()->flash('error', 'Ocurrió un error al subir algunos archivos nuevos.');
                }
            }
        }

        try {
            $data = $request->except(['attachments', 'existing_attachment_urls']);
            $data['attachment_urls'] = $updatedUrls;

            $interaction->update($data);

            return redirect()->route('interactions.index')->with('success', 'Interacción actualizada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al actualizar la interacción: ' . $e->getMessage());
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error al actualizar la interacción: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una interacción específica (for Web requests).
     *
     * @param  \App\Models\Interaction  $interaction
     * @return \Illuminate\Http\Response
     */
    public function destroyWeb(Interaction $interaction)
    {
        try {
            if ($interaction->attachment_urls) {
                foreach ($interaction->attachment_urls as $url) {
                    $fileId = $this->googleDriveService->getFileIdFromUrl($url);
                    if ($fileId) {
                        $this->googleDriveService->deleteFile($fileId);
                    } else {
                        Log::warning('No se pudo extraer el ID del archivo de Google Drive de la URL: ' . $url);
                    }
                }
            }

            $interaction->delete();

            return redirect()->route('interactions.index')->with('success', 'Interacción eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar la interacción o sus archivos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar la interacción: ' . $e->getMessage());
        }
    }
}
