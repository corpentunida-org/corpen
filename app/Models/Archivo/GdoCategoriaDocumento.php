<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoCategoriaDocumento extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'gdo_categoria_documento';

    // Campos asignables masivamente
    protected $fillable = [
        'nombre',
    ];


    /**
     * Relación con los tipos de documento.
     */
    public function tiposDocumento()
    {
        return $this->hasMany(GdoTipoDocumento::class, 'categoria_documento_id', 'id');
    }
}
