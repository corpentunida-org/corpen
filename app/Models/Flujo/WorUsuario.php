<?php

namespace App\Models\Flujo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Flujo\Workflow;

class WorUsuario extends Pivot
{
    use HasFactory;

    /**
     * El nombre exacto de la tabla en la BD.
     */
    protected $table = 'wor_usuarios';

    /**
     * Indicamos que la tabla TIENE un id autoincremental.
     * Por defecto los modelos Pivot asumen que no hay ID, 
     * pero en tu migración pusimos $table->id();
     */
    public $incrementing = true;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'workflow_id',
        // Si agregas campos extra en el futuro (ej: 'rol_en_proyecto'), añádelos aquí.
    ];

    /**
     * Relación inversa hacia el Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación inversa hacia el Workflow.
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}