<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoTipoDocumento extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_tipo_documento';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación con documentos de empleados.
     */
    public function documentos()
    {
        return $this->hasMany(GdoDocsEmpleados::class, 'tipo_documento_id','id');
    }
}
