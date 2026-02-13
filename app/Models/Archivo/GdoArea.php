<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Archivo\GdoCargo;
use App\Models\Interacciones\Interaction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class GdoArea extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_area';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'GDO_cargo_id',
        'estado',
    ];

    /**
     * MEJORA: Se utiliza $casts en lugar de $dates (obsoleto).
     * Esto garantiza que created_at siempre sea un objeto Carbon 
     * y evita el error "format() on null" en la vista.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'estado'     => 'string',
    ];

    // --- SCOPES (Consultas Profesionales) ---

    /**
     * Scope para filtrar áreas activas fácilmente.
     * Uso: GdoArea::active()->get();
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('estado', 'activo');
    }

    // --- MUTATORS & ACCESSORS ---

    /**
     * Mutator para guardar el estado siempre en minúsculas.
     */
    public function setEstadoAttribute($value)
    {
        $this->attributes['estado'] = strtolower($value);
    }

    /**
     * Accessor para mostrar el nombre en formato capitalizado.
     */
    public function getNombreAttribute($value)
    {
        return ucwords($value);
    }

    // --- RELACIONES (Mantenidas y Mejoradas) ---

    /**
     * Cargo que es jefe del área.
     * MEJORA: Al estar la llave 'GDO_cargo_id' en esta tabla, 
     * lo técnicamente correcto es BelongsTo para optimizar el JOIN.
     */
    public function jefeCargo(): BelongsTo
    {
        return $this->belongsTo(GdoCargo::class, 'GDO_cargo_id', 'id');
    }

    /**
     * Cargos que pertenecen a esta área.
     */
    public function cargos(): HasMany
    {
        return $this->hasMany(GdoCargo::class, 'GDO_area_id', 'id');
    }

    /**
     * Interacciones creadas por el área.
     */
    public function interacciones(): HasMany
    {
        return $this->hasMany(Interaction::class, 'id_area', 'id');
    }

    /**
     * Interacciones asignadas al área.
     */
    public function interaccionesAsignadas(): HasMany
    {
        return $this->hasMany(Interaction::class, 'id_area_de_asignacion', 'id');
    }

    /**
     * Flujos de solicitudes asociados a esta área.
     */
    public function flujos(): HasMany
    {
        return $this->hasMany(\App\Models\Correspondencia\FlujoDeTrabajo::class, 'id_area', 'id');
    }

}