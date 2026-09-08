<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// Importaciones de modelos relacionados
use App\Models\Maestras\MaeTerceros;
use App\Models\Certificados\CarSiaBloque;
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
        'numero_bloque',
        'id_tercero',
    ];

    // 3. Casteo de variables (Optimización de tipos)
    protected $casts = [
        'numero_bloque' => 'integer',
    ];

    // ---------------------------------------------------
    // 4. RELACIONES (El corazón del sistema)
    // ---------------------------------------------------

    /**
     * Tercero asociado a la operación.
     */
    public function tercero(): BelongsTo
    {
        // Se especifica que la llave foránea es 'id_tercero' y la llave local en MaeTerceros es 'cod_ter'
        return $this->belongsTo(MaeTerceros::class, 'id_tercero', 'cod_ter');
    }

    // ==========================================
    // RELACIONES INDIVIDUALES (Operación específica)
    // ==========================================

    public function estados(): HasMany
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'id_car_sia_operaciones');
    }

    public function tipos(): HasMany
    {
        return $this->hasMany(CarSiaTipoOperacion::class, 'id_car_sia_operaciones');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'id_car_sia_operaciones');
    }

    // ==========================================
    // RELACIONES MASIVAS (A nivel de Lote/Bloque)
    // ==========================================

    public function estadosBloque(): HasMany
    {
        return $this->hasMany(CarSiaEstadoOperacion::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    public function tiposBloque(): HasMany
    {
        return $this->hasMany(CarSiaTipoOperacion::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    public function alertasBloque(): HasMany
    {
        return $this->hasMany(CarSiaOperacionAlerta::class, 'numero_bloque', 'numero_bloque')
                    ->whereNull('id_car_sia_operaciones');
    }

    // ==========================================
    // OTRAS RELACIONES (Configuraciones y Detalle)
    // ==========================================

    /**
     * Historial de configuraciones asignadas (Histórico completo)
     */
    public function configuraciones(): HasMany
    {
        return $this->hasMany(CarSiaOperacionConfig::class, 'id_car_sia_operaciones');
    }

    /**
     * Relación con la configuración actual/activa asignada a esta operación.
     */
    public function configuracion(): HasOne
    {
        return $this->hasOne(CarSiaOperacionConfig::class, 'id_car_sia_operaciones', 'id');
    }

    /**
     * Detalle de las líneas asociadas a esta operación.
     */
    public function lineas(): HasMany
    {
        return $this->hasMany(CarSiaOperacionLinea::class, 'id_car_sia_operaciones');
    }

    /**
     * Relación: Esta operación pertenece a un bloque específico.
     */
    public function bloque(): BelongsTo
    {
        return $this->belongsTo(CarSiaBloque::class, 'numero_bloque', 'numero_bloque');
    }
}
