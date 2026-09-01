<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CarSiaOperacionAlerta extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones_alertas';

    // 2. Configuración de la Llave Primaria Personalizada (¡Muy importante!)
    // Como el ID es un VARCHAR(50) según tu migración, debemos decirle a Eloquent:
    public $incrementing = false; // No es auto-numérico
    protected $keyType = 'string'; // Es una cadena de texto

    // 3. Campos asignables masivamente
    protected $fillable = [
        'id', // Lo incluimos en el fillable porque lo vas a generar/asignar manualmente
        'id_car_sia_tipos_alerta',
        'numero_bloque',
        'id_car_sia_operaciones', //Null si asigna desde el index solo se hace un registro de alerta, y solo se toma "numero_bloque"
        'fecha_programada',
        'procesado_en',
        'id_user',
    ];

    // 4. Conversión de tipos de datos (Casting)
    // Le indicamos a Laravel que trate estos campos como objetos Carbon (fechas)
    protected $casts = [
        'fecha_programada' => 'datetime',
        'procesado_en' => 'datetime',
    ];

    // 5. Relaciones

    // Pertenece a un tipo de alerta del catálogo
    public function tipoAlerta()
    {
        return $this->belongsTo(CarSiaTipoAlerta::class, 'id_car_sia_tipos_alerta');
    }

    // Puede pertenecer a una operación específica (es nullable según tu diseño)
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }

    // Pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
