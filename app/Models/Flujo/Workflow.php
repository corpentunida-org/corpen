<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Flujo\Task;

class Workflow extends Model
{
    use HasFactory;
    protected $table = 'wor_workflows';
    /**
     * Campos que se pueden asignar masivamente.
     * Se han añadido todas las nuevas columnas de la tabla.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'activo',
        'es_plantilla',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        'configuracion',
        'creado_por',
        'modificado_por',
        'asignado_a'
    ];

    /**
     * Conversión de tipos para los atributos del modelo.
     * Esto es crucial para que Laravel trate correctamente los campos
     * como booleanos, fechas o arrays (para JSON).
     */
    protected $casts = [
        'activo' => 'boolean',
        'es_plantilla' => 'boolean',
        'configuracion' => 'array', // Convierte el JSON de la BD a un array de PHP
        'fecha_inicio' => 'date',   // Convierte la fecha a una instancia de Carbon
        'fecha_fin' => 'date',     // Convierte la fecha a una instancia de Carbon
        'estado' => 'string',
        'prioridad' => 'string',
    ];

    /**
     * Relación con el usuario que creó el flujo.
     */
    public function creator() {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con el usuario que modificó el flujo por última vez.
     */
    public function modifier() {
        return $this->belongsTo(User::class, 'modificado_por');
    }
    
    /**
     * Relación con el usuario que modificó el flujo por última vez.
     */
    public function asignado() {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /**
     * Relación con las tareas del flujo.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'workflow_id', 'id');
    }
    public function participantes()
    {
        return $this->belongsToMany(User::class, 'wor_usuarios', 'workflow_id', 'user_id')
                    ->using(\App\Models\Flujo\WorUsuario::class)
                    ->withTimestamps();
    }
}