<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Maestras\MaeTerceros;

/**
 * Cabecera Maestra del Motor de Operaciones.
 */
class CarSiaOperacion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones';

    // 2. Campos asignables masivamente
    // - numero_radicado: Radicado generado a partir de la estructura definida en car_sia_tipos_evento.estructura_radicado.
    // - numero_bloque: Identificador generado durante la ejecución del proceso y compartido por los registros pertenecientes al mismo.
    protected $fillable = [
        'numero_radicado',
        'numero_bloque',
        'id_factura',
        'id_tercero',
    ];

    // 3. Relaciones (El corazón del sistema)

    /**
     * Factura asociada a la operación.
     */
    public function factura()
    {
        return $this->belongsTo(CarSiaApi::class, 'id_factura');
    }

    /**
     * Tercero asociado a la operación.
     */
    public function tercero()
    {
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
        return $this->hasMany(CarSiaTipoOperacion::class, 'id_car_sia_operaciones');
    }

    // El historial de estados por los que ha pasado la operación
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
