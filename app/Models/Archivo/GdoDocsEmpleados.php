<?php

namespace App\Models\Archivo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GdoDocsEmpleados extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'gdo_docs_empleados';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'empleado_id',
        'tipo_documento_id',
        'ruta_archivo',
        'fecha_subida',
        'observaciones',
    ];

    // Campos que serán tratados como fechas por Eloquent
    protected $casts = [
        'fecha_subida' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Relación con el empleado
     * Asumiendo que 'empleado_id' referencia 'cedula' en la tabla empleados
     */
    public function empleado()
    {
        return $this->belongsTo(GdoEmpleado::class, 'empleado_id', 'cedula');
    }

    /**
     * Relación con el tipo de documento
     */
    public function tipoDocumento()
    {
        return $this->belongsTo(GdoTipoDocumento::class, 'tipo_documento_id', 'id');
    }

    /**
     * Accesor para mostrar solo el nombre del archivo sin la ruta
     */
    public function getNombreArchivoAttribute()
    {
        return basename($this->ruta_archivo);
    }
}
