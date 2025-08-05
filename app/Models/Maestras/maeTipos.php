<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Maestras\maeTerceros;
use App\Models\User;


class MaeTipos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'MaeTipos'; // Especificamos el nombre exacto de la tabla

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'grupo',
        'categoria',
        'orden',
        'activo',
        'editable',
        'eliminable',
        'created_by',
        'updated_by',
    ];

    /**
     * Las fechas que deben tratarse como Carbon (fecha).
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relación con el usuario que creó el registro.
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el registro.
     */
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function maeTipos()
    {
        return $this->belongsTo(maeTipos::class, 'codigo', 'tip_prv');
    }
    public function maeTerceros()
    {
        // Esto le dice a Laravel:
        // "Busca en la tabla MaeTerceros todos los registros donde la columna 'tip_prv'
        // coincida con la columna 'codigo' de este MaeTipo."
        return $this->hasMany(MaeTerceros::class, 'tip_prv', 'codigo');
    }
}
