<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoFuncion extends Model
{
    use HasFactory;

    // Nombre de la tabla según tu imagen
    protected $table = 'gdo_funcion';

    // Campos habilitados para asignación masiva
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    /**
     * Relación Muchos a Muchos con GdoCargo.
     */
    public function cargos()
    {
        return $this->belongsToMany(
            GdoCargo::class,         // Apunta al modelo en el mismo namespace
            'gdo_funcion_cargo',     // Tabla pivot
            'gdo_funcion_id',        // FK de funciones en pivot
            'gdo_cargo_id'           // FK de cargos en pivot
        )->withPivot('id', 'estado') // Campos extra solicitados
         ->withTimestamps();
    }
}