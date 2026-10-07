<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Modelo: CatalogoInmueble
 * Propósito: Gestionar el inventario de espacios y características físicas
 *            de los inmuebles disponibles para el sistema de reservas.
 * Tabla asociada: rsv_catalogo_inmueble
 */
class CatalogoInmueble extends Model
{
    /**
     * Definición explícita de la tabla en la base de datos debido al prefijo del módulo.
     */
    protected $table = 'rsv_catalogo_inmueble';

    /**
     * Atributos habilitados para asignación masiva (Mass Assignment Protection).
     */
    protected $fillable = [
        'name',              // Nombre comercial o título del inmueble
        'city',              // Ciudad de ubicación
        'ubicacion',         // Dirección o detalles físicos de localización
        'active',            // Estado de disponibilidad (1 = Activo, 0 = Inactivo)
        'capacidad_maxima',  // Límite máximo de huéspedes permitidos
        'tipo_inmueble_id',  // Relación lógica con el tipo de inmueble
    ];

    /**
     * Casteo automático de atributos a tipos de datos nativos de PHP.
     */
    protected $casts = [
        'active' => 'boolean',
        'capacidad_maxima' => 'integer',
    ];

    /**
     * RELACIÓN: Un inmueble posee un historial completo de tarifas por temporada.
     */
    public function tarifasTemporadas(): HasMany
    {
        return $this->hasMany(TarifaTemporada::class, 'id_rsv_catalogo_inmueble');
    }

    /**
     * RELACIÓN OPTIMIZADA: Obtiene de manera directa la última tarifa de temporada registrada.
     * Utiliza latestOfMany() para optimizar el rendimiento y evitar consultas N+1.
     */
    public function latestTarifa(): HasOne
    {
        return $this->hasOne(TarifaTemporada::class, 'id_rsv_catalogo_inmueble')->latestOfMany();
    }

    /**
     * RELACIÓN: Un inmueble puede tener múltiples bloqueos de fechas en el calendario
     * para prevenir el overbooking.
     */
    public function bloqueosCalendario(): HasMany
    {
        return $this->hasMany(BloqueoCalendario::class, 'id_rsv_catalogo_inmueble');
    }

    /**
     * RELACIÓN: Un inmueble cuenta con una galería multimedia (imágenes o vídeos promocionales).
     */
    public function multimedia(): HasMany
    {
        return $this->hasMany(InmuebleMultimedia::class, 'id_rsv_catalogo_inmueble');
    }

    /**
     * RELACIÓN: Un inmueble concentra múltiples transacciones de reservas a lo largo del tiempo.
     */
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'id_rsv_catalogo_inmueble');
    }
}

