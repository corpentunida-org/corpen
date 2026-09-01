<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Importaciones de modelos relacionados
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
        'numero_radicado',
        'numero_bloque',   // INT
        'id_tercero',
    ];

    // ---------------------------------------------------
    // 3. RELACIONES (El corazón del sistema)
    // ---------------------------------------------------

    /**
     * Tercero asociado a la operación.
     */
    public function tercero()
    {
        // Se especifica que la llave foránea es 'id_tercero' y la llave local en MaeTerceros es 'cod_ter'
        return $this->belongsTo(MaeTerceros::class, 'id_tercero', 'cod_ter');
    }

    // ==========================================
    // RELACIONES INDIVIDUALES (Operación específica)
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
    // RELACIONES MASIVAS (A nivel de Lote/Bloque)
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
    // OTRAS RELACIONES (Configuraciones y Detalle)
    // ==========================================

    public function configuraciones()
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_operaciones');
    }

    /**
     * Detalle de las líneas asociadas a esta operación.
     */
    public function lineas()
    {
        // Esto asume que la llave primaria de esta tabla (car_sia_operaciones) es 'id'.
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_operaciones');
    }
    /**
     * Relación: Esta operación pertenece a un bloque específico.
     * Enlazamos la columna local 'numero_bloque' con la llave foránea 'numero_bloque' del modelo CarSiaBloque.
     */
    public function bloque()
    {
        return $this->belongsTo(CarSiaBloque::class, 'numero_bloque', 'numero_bloque');
    }
}
