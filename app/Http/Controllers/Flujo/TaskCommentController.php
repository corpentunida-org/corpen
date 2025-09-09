<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flujo\TaskComment;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public function index()
    {
        $comments = TaskComment::with('task', 'user')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'comentario' => 'required|string',
        ]);

        $data['user_id'] = Auth::id();

        TaskComment::create($data);

        return redirect()->route('flujo.tasks.edit', $request->task_id)
            ->with('success', '💬 Comentario agregado correctamente.');
    }


    public function show(TaskComment $comment)
    {
        $comment->load('task','user');
        return response()->json($comment);
    }

    public function destroy(TaskComment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comentario eliminado']);
    }
}

