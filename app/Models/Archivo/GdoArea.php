<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Archivo\GdoCargo;

use App\Models\Interacciones\Interaction;

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
     * Campos que serán tratados como fechas por Eloquent.
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

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

    // Cargo que es jefe del área
    public function jefeCargo()
    {
        return $this->hasOne(GdoCargo::class, 'id', 'GDO_cargo_id');
    }

    // Cargos que pertenecen a esta área
    public function cargos()
    {
        return $this->hasMany(GdoCargo::class, 'GDO_area_id', 'id');
    }

    public function interacciones()
    {
        return $this->hasMany(Interaction::class, 'id_area', 'id');
    }

    public function interaccionesAsignadas()
    {
        return $this->hasMany(Interaction::class, 'id_area_de_asignacion', 'id');
    }

    

}
