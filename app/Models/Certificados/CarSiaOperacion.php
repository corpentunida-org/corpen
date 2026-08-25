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
use App\Models\Certificados\CarSiaOperacionLinea;

/**
 * Cabecera Maestra del Motor de Operaciones.
 */
class CarSiaOperacion extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_operaciones';

    // 2. Campos asignables masivamente
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

    // ==========================================
    // 1. RELACIONES INDIVIDUALES (Operación específica)
    // ==========================================
    public function estados()
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'id_car_sia_operaciones');
    }

    public function tipos()
    {
        return $this->hasMany(CarSiaTipoOperacion::class, 'id_car_sia_operaciones');
    }

    public function alertas()
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'id_car_sia_operaciones');
    }

    // ==========================================
    // 2. RELACIONES MASIVAS (A nivel de Lote/Bloque)
    // ==========================================
    public function estadosBloque()
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    public function tiposBloque()
    {
        return $this->hasMany(CarSiaTipoOperacion::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    public function alertasBloque()
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    // ==========================================
    // 3. OTRAS RELACIONES (Configuraciones y Detalle)
    // ==========================================
    public function configuraciones()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_operaciones');
    }

    public function lineas()
    {
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_operaciones');
    }
}
