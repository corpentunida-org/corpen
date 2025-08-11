<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoDocsEmpleados extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'gdo_docs_empleados';

    /**
     * Campos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'ruta_archivo',
        'fecha_subida',
        'observaciones',
    ];

    /**
     * Campos que serán tratados como fechas por Eloquent.
     */
    protected $dates = [
        'fecha_subida',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación con empleado (opcional para cuando se agregue la FK).
     */
    // public function empleado()
    // {
    //     return $this->belongsTo(GdoEmpleado::class, 'empleado_id');
    // }

    /**
     * Accesor para mostrar solo el nombre del archivo sin la ruta.
     */
    public function getNombreArchivoAttribute()
    {
        return basename($this->ruta_archivo);
    }
}
