<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\ComunicacionSalida;
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Plantilla;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

class ComunicacionSalidaController extends Controller
{
    /**
     * Listado de comunicaciones.
     */
    public function index(Request $request)
    {
        // 1. Cargamos con relaciones para evitar N+1 y asegurar datos
        $query = ComunicacionSalida::with(['correspondencia.remitente', 'usuario']);

        // 2. Filtro de búsqueda (Mejorado para evitar errores de tipo)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nro_oficio_salida', 'LIKE', "%{$search}%")
                ->orWhereHas('correspondencia', function($sub) use ($search) {
                    $sub->where('asunto', 'LIKE', "%{$search}%")
                        ->orWhere('id_radicado', 'LIKE', "%{$search}%");
                });
            });
        }

        // 3. Filtro de Estado
        if ($request->filled('estado')) {
            $query->where('estado_envio', $request->estado);
        }

        $comunicaciones = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('correspondencia.comunicaciones_salida.index', compact('comunicaciones'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        // Solo cargamos radicados que NO tengan una salida registrada
        $correspondencias = Correspondencia::whereDoesntHave('comunicacionSalida')->get();
        
        $plantillas = Plantilla::all();
        $usuarios = User::orderBy('name', 'asc')->get();
        
        return view('correspondencia.comunicaciones_salida.create', compact('correspondencias', 'plantillas', 'usuarios'));
    }
    /**
     * Almacenar nueva comunicación con Folio automático, Firmante elegido y Firma en S3.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'cuerpo_carta'       => 'required|string',
            'firma_digital'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'estado_envio'       => 'required|in:Generado,Enviado por Email,Notificado Físicamente', 
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fk_usuario'         => 'required|exists:users,id',
            'fecha_generacion'   => 'nullable|date', // Cambiado a nullable para evitar errores
        ]);

        // 1. Si la fecha no viene en el request, asignar hoy
        if (!$request->filled('fecha_generacion')) {
            $data['fecha_generacion'] = now();
        }

        // 2. Generación automática del Folio (OF-YYYY-XXXX)
        $anioActual = date('Y');
        $ultimoId = ComunicacionSalida::max('id_respuesta') ?? 0;
        $nuevoConsecutivo = str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);
        $data['nro_oficio_salida'] = "OF-{$anioActual}-{$nuevoConsecutivo}";

        // 3. Gestión de la Firma Digital en AWS S3
        if ($request->hasFile('firma_digital')) {
            $path = $request->file('firma_digital')->store('firmas', 's3');
            $data['ruta_pdf'] = $path; 
        }

        // 4. Crear registro
        ComunicacionSalida::create($data);

        // 5. Redirección con el ID del radicado para volver al detalle
        return redirect()->route('correspondencia.correspondencias.show', $data['id_correspondencia'])
            ->with('success', "Comunicación registrada correctamente con el Folio: {$data['nro_oficio_salida']}");
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
        $usuarios = User::orderBy('name', 'asc')->get();
        
        return view('correspondencia.comunicaciones_salida.edit', compact('comunicacionSalida', 'correspondencias', 'plantillas', 'usuarios'));
    }

    /**
     * Actualizar comunicación.
     */
    public function update(Request $request, ComunicacionSalida $comunicacionSalida)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'cuerpo_carta'       => 'required|string',
            'estado_envio'       => 'required|in:Generado,Enviado por Email,Notificado Físicamente',
            'id_plantilla'       => 'nullable|exists:corr_plantillas,id',
            'fecha_generacion'   => 'nullable|date',
            'firma_digital'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fk_usuario'         => 'required|exists:users,id',
        ]);

        // Si suben una nueva firma, reemplazamos en S3
        if ($request->hasFile('firma_digital')) {
            if ($comunicacionSalida->ruta_pdf) {
                Storage::disk('s3')->delete($comunicacionSalida->ruta_pdf);
            }
            $data['ruta_pdf'] = $request->file('firma_digital')->store('firmas', 's3');
        }

        $comunicacionSalida->update($data);

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación actualizada correctamente.');
    }

    /**
     * Eliminar comunicación y su archivo en S3.
     */
    public function destroy(ComunicacionSalida $comunicacionSalida)
    {
        if ($comunicacionSalida->ruta_pdf) {
            Storage::disk('s3')->delete($comunicacionSalida->ruta_pdf);
        }
        
        $comunicacionSalida->delete();

        return redirect()->route('correspondencia.comunicaciones-salida.index')
            ->with('success', 'Comunicación eliminada.');
    }

    /**
     * Generar PDF con Fondo local y Firma de AWS (ambos en Base64).
     */
    public function descargarPdf($id)
    {
        // Cargamos la relación usuario para que el PDF sepa quién firma
        $comunicacion = ComunicacionSalida::with(['usuario', 'correspondencia.remitente'])->findOrFail($id);

        // 1. Procesar Fondo JPG local a Base64
        $pathFondo = resource_path('views/correspondencia/comunicaciones_salida/fondo_de_pdf.jpg');
        $fondoBase64 = null;
        if (file_exists($pathFondo)) {
            $fondoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($pathFondo));
        }

        // 2. Procesar Firma desde AWS S3 a Base64 para DomPDF
        $firmaBase64 = null;
        if ($comunicacion->ruta_pdf) {
            try {
                if (Storage::disk('s3')->exists($comunicacion->ruta_pdf)) {
                    $fileContent = Storage::disk('s3')->get($comunicacion->ruta_pdf);
                    $extension = pathinfo($comunicacion->ruta_pdf, PATHINFO_EXTENSION);
                    $firmaBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($fileContent);
                }
            } catch (\Exception $e) {
                // Si falla la conexión a S3, el PDF se generará sin firma para evitar error 500
            }
        }

        // 3. Generar PDF
        $pdf = Pdf::loadView('correspondencia.comunicaciones_salida.pdf', [
            'comunicacionSalida' => $comunicacion,
            'fondoImg' => $fondoBase64,
            'firmaImg' => $firmaBase64
        ]);

        return $pdf->download('oficio_' . $comunicacion->nro_oficio_salida . '.pdf');
    }
}