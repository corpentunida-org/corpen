<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoProceso extends Model
{
    use HasFactory;

    // Nombre exacto de la tabla según la imagen proporcionada
    protected $table = 'corr_estados_procesos';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'id_estado',
        'id_proceso',
        'detalle',
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
     * asegúrate de añadir: use App\Models\Estado; al inicio del archivo.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }
}