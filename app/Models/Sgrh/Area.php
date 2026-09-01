<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

/**
 * Área de la estructura organizacional. Catálogo propio de SGRH — sus filas iniciales se
 * copiaron una sola vez desde gdo_area (módulo Archivo/"Gestión"), pero no queda enlazada a
 * esa tabla: gdo_area sigue existiendo aparte, sin sincronización posterior.
 */
class Area extends Model
{
    protected $table = 'sgrh_areas';

    protected $fillable = ['nombre', 'descripcion', 'cargo_responsable_id', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class, 'sgrh_area_id');
    }

    /**
     * Cargo "jefe" de esta área (equivalente a GdoArea::jefeCargo() del módulo legado).
     */
    public function cargoResponsable()
    {
        return $this->belongsTo(Cargo::class, 'cargo_responsable_id');
    }
}
