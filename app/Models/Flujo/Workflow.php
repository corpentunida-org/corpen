<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Workflow extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'creado_por'
    ];

    /**
     * Relación con el usuario que creó el flujo
     */
    public function creator() {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con las tareas del flujo
     */
    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
