<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Maestras\MaeTerceros;

class CarSiaOperacion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'numero_radicado',
        'numero_bloque',
        'id_factura',
        'id_tercero',
    ];

    // 3. Relaciones (El corazón del sistema)

    // Relación con el staging/factura
    public function factura()
    {
        return $this->belongsTo(CarSiaApi::class, 'id_factura');
    }

    public function tercero()
    {
        // Nota cómo especificamos 'cod_ter' porque esa es la llave primaria en MaeTerceros
        return $this->belongsTo(MaeTerceros::class, 'id_tercero', 'cod_ter');
    }

    // --- Relaciones hacia las tablas Pivote (Fase 3) ---

    public function alertas()
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'id_car_sia_operaciones');
    }

    public function configuraciones()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_operaciones');
    }

    public function tiposEvento()
    {
        return $this->hasMany(CarSiaTipoEvento::class, 'id_car_sia_operaciones');
    }

    public function estados()
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'id_car_sia_operaciones');
    }

    // --- Relaciones hacia el Detalle Operativo (Fase 4) ---

    public function lineas()
    {
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_operaciones');
    }
}
