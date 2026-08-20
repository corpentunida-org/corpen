<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarSiaEventoAuditoria extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_eventos_auditoria';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'nombre',
    ];

    // 3. Relaciones (Opcional)
    // Un evento de auditoría puede tener muchísimos registros asociados en los logs
    public function logs()
    {
        return $this->hasMany(CarSiaOperacionLog::class, 'id_car_sia_eventos_auditoria');
    }
}
