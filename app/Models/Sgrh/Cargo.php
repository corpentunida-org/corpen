<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

/**
 * Cargo del catálogo de RR. HH. Catálogo propio de SGRH — sus filas iniciales se copiaron una
 * sola vez desde gdo_cargo (módulo Archivo/"Gestión"), incluyendo el contacto corporativo del
 * cargo (no de la persona: eso vive en MaeTerceros). gdo_cargo sigue existiendo aparte, sin
 * sincronización posterior.
 */
class Cargo extends Model
{
    protected $table = 'sgrh_cargos';

    protected $fillable = [
        'nombre', 'sgrh_area_id', 'salario_base', 'jornada',
        'telefono_corporativo', 'celular_corporativo', 'ext_corporativo',
        'correo_corporativo', 'gmail_corporativo',
        // manual_funciones: ruta S3 heredada de gdo_cargo.manual_funciones. No hay UI de carga
        // en SGRH todavía (necesitaría el mismo flujo de GdoEmpleadoController::storeDocumento).
        'manual_funciones',
        'observaciones', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'salario_base' => 'decimal:2',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'sgrh_area_id');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'cargo_id');
    }
}
