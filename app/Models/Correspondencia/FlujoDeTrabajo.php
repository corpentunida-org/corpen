<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Archivo\GdoArea; // Importante importar el modelo relacionado

class FlujoDeTrabajo extends Model
{
    use HasFactory;

    protected $table = 'corr_flujo_de_trabajo';

    protected $fillable = [
        'nombre',
        'detalle',
        'id_area',    // El área/cargo seleccionado
        'usuario_id', // ID del jefe o usuario responsable
    ];

    /**
     * Relación: Un flujo de trabajo pertenece a un Área.
     */
    public function area()
    {
        return $this->belongsTo(GdoArea::class, 'id_area');
    }

    /**
     * Relación: Pertenece a un usuario (Jefe/Responsable).
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function correspondencias()
    {
        return $this->hasMany(Correspondencia::class, 'flujo_id');
    }

    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'flujo_id');
    }
    public function trd()
    {
        // hasOne(Modelo, clave_foranea_en_trd, clave_local)
        return $this->hasOne(Trd::class, 'fk_flujo', 'id');
    }
}