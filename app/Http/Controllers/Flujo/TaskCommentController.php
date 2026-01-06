<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\TaskComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskCommentController extends Controller
{
    /**
     * Listado para el Panel de Control con Trazabilidad
     */
    public function index()
    {
        $comments = TaskComment::with(['user', 'task.workflow'])
            ->orderByDesc('created_at')
            ->paginate(10);

        if (request()->wantsJson()) {
            return response()->json($comments);
        }

        return view('flujo.comments.index', compact('comments'));
    }

    /**
     * Almacenar feedback con soporte en AWS S3
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id'    => 'required|exists:tasks,id',
            'comentario' => 'required|string|min:3',
            'soporte'    => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240', // 10MB Máx
        ]);

        $data['user_id'] = Auth::id();

        // ✅ Lógica de Carga a AWS S3
        if ($request->hasFile('soporte')) {
            $file = $request->file('soporte');

            // 1. Limpieza y normalización del nombre
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension    = $file->getClientOriginalExtension();
            $cleanName    = Str::slug($originalName, '-');

            // 2. Estructura de ruta corporativa
            $userId     = Auth::id();
            $year       = now()->year;
            $month      = now()->format('m');
            $filename   = time() . '_' . $cleanName . '.' . $extension;
            
            // Ruta: corpentunida/soportes/usuario_X/2026/01/archivo.pdf
            $folderPath = "corpentunida/soportes/usuario_{$userId}/{$year}/{$month}";

            // 3. Ejecución de subida a S3
            $path = Storage::disk('s3')->putFileAs($folderPath, $file, $filename);

            // 4. Asignamos la ruta al array de datos
            $data['soporte'] = $path;
        }

        TaskComment::create($data);

        return back()->with('success', '💬 Feedback y evidencia registrados en el sistema de trazabilidad.');
    }

    public function show(TaskComment $comment)
    {
        $comment->load('user', 'task.workflow');
        return response()->json($comment);
    }

    /**
     * Eliminación con limpieza de archivos en la nube
     */
    public function destroy(TaskComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        // ✅ Lógica de limpieza: Borrar archivo de S3 si existe
        if ($comment->soporte) {
            Storage::disk('s3')->delete($comment->soporte);
        }

        $comment->delete();

        return response()->json(['message' => 'Registro de feedback y evidencia eliminados']);
    }
}