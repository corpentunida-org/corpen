<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoProceso extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla
    protected $table = 'corr_estados_procesos';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'id_estado',
        'id_proceso',
        'detalle',
        'activo',
    ];

    /**
     * Casting de tipos de datos.
     * Convierte el tinyint(1) de la base de datos a un booleano nativo en PHP.
     */
    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con el Proceso (Paso del flujo)
     * Se asume que el modelo Proceso también está en App\Models\Correspondencia
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'id_proceso');
    }

    /**
     * Relación con el Estado
     * Si el modelo Estado está en App\Models (fuera de Correspondencia), 
     * asegúrate de importarlo en la cabecera (use App\Models\Estado;).
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }
}