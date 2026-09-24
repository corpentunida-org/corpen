<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * "Línea de Obligación" propia de Interacciones, por área — independiente de
 * App\Models\Creditos\LineaCredito (líneas de crédito de Cartera). Los ids de las líneas de
 * Cartera se preservaron iguales al migrar (ver 2026_09_24_090000_...), así que
 * Interaction::id_linea_de_obligacion sigue resolviendo bien tanto para lo histórico como para
 * lo nuevo.
 */
class IntLinea extends Model
{
    use HasFactory;

    protected $table = 'int_lineas';

    protected $fillable = ['id', 'name', 'area'];

    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'id_linea_de_obligacion', 'id');
    }
}
