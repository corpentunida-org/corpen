<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comentario'
    ];

    /**
     * Tarea a la que pertenece el comentario
     */
    public function task() {
        return $this->belongsTo(Task::class);
    }

    /**
     * Usuario que hizo el comentario
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(TaskComment::class);
    }

}
