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


    
    public function tiposDocumento()
    {
        return $this->hasMany(GdoTipoDocumento::class, 'categoria_documento_id', 'id');
    }

    /**
     * Determina si la categoría se puede eliminar legalmente.
     */
    public function esEliminable(): bool
    {
        // Si tiene 0 tipos de documento, es eliminable.
        return $this->tiposDocumento()->count() === 0;
    }

}
