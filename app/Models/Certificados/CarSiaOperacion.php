<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Maestras\MaeTerceros;
use App\Models\Certificados\CarSiaOperacionAlerta;
use App\Models\Certificados\CarSiaOperacionConfig;
use App\Models\Certificados\CarSiaEstadoOperacion;
use App\Models\Certificados\CarSiaTipoOperacion;

/**
 * Cabecera Maestra del Motor de Operaciones.
 */
class CarSiaOperacion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones';

    // 2. Campos asignables masivamente
    // - numero_radicado: Radicado generado a partir de la estructura definida en car_sia_tipos_operacion.estructura_radicado.
    // - numero_bloque: Identificador generado durante la ejecución del proceso y compartido por los registros pertenecientes al mismo.
    protected $fillable = [
        'numero_radicado', //
        'numero_bloque', //INT
        'id_tercero',
    ];

    // 3. Relaciones (El corazón del sistema)

    /**
     * Tercero asociado a la operación.
     */
    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'id_tercero', 'cod_ter');
    }

    // --- Relaciones hacia las tablas Pivote (Fase 3) ---

    /**
     * Historial de Alertas Programadas
     * Al igual que estados y tipos, lo conectamos por bloque para mantener el mismo flujo.
     */
    public function alertas()
    {
        return $this->hasMany(
            CarSiaOperacionAlerta::class,
            'numero_bloque',
            'numero_bloque'
        );
    }

    public function configuraciones()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_operaciones');
    }

    /**
     * Historial de Estados de la Operación
     * Lo enlazamos por 'numero_bloque' para que cargue los registros iniciales del ETL
     * donde el id_car_sia_operaciones está en null.
     */
    public function estados()
    {
        return $this->hasMany(
            CarSiaEstadoOperacion::class,
            'numero_bloque', // Llave foránea en la tabla del historial
            'numero_bloque'  // Llave local en la tabla de operaciones
        );
    }

    /**
     * Historial de Tipos / Eventos de la Operación
     * Lo enlazamos por 'numero_bloque' para que cargue los registros iniciales del ETL.
     */
    public function tipos()
    {
        return $this->hasMany(
            CarSiaTipoOperacion::class,
            'numero_bloque', // Llave foránea en la tabla del historial
            'numero_bloque'  // Llave local en la tabla de operaciones
        );
    }

    // --- Relaciones hacia el Detalle Operativo (Fase 4) ---

    public function lineas()
    {
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_operaciones');
    }
}
