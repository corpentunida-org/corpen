<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Catálogo de Orígenes de Evento.
 * Identificador único del origen del evento y su nombre descriptivo del origen del evento (ej. Interfaz Web, Cron, API).
 */
class CarSiaOrigenEvento extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_origenes_evento';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
    ];

    // 3. Relaciones
    /**
     * Un origen de evento puede tener muchísimos registros en la tabla de logs.
     */
    public function logs()
    {
        return $this->hasMany(CarSiaOperacionLog::class, 'id_car_sia_origenes_evento');
    }
}
