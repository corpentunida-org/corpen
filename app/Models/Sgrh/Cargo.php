<?php

namespace App\Models\Sgrh;

use Illuminate\Database\Eloquent\Model;

/**
 * Cargo del catálogo de RR. HH. Catálogo propio de SGRH — sus filas iniciales se copiaron una
 * sola vez desde gdo_cargo (módulo Archivo/"Gestión"). gdo_cargo sigue existiendo aparte, sin
 * sincronización posterior.
 *
 * Sin salario_base ni contacto corporativo propio: un cargo puede tener varias personas (a
 * diferencia de gdo_cargo, que era 1 cargo = 1 persona), así que esos datos viven por
 * colaborador (Empleado::telefono_corporativo/... y Contrato::salario_contrato), no aquí.
 */
class Cargo extends Model
{
    protected $table = 'sgrh_cargos';

    protected $fillable = [
        'nombre', 'sgrh_area_id', 'jornada',
        // Cadena de aprobación (jerarquía), preparación para el motor de permisos/vacaciones:
        // ambos apuntan a otro cargo del mismo catálogo.
        'jefe_inmediato_id', 'director_id',
        // manual_funciones: ruta S3 heredada de gdo_cargo.manual_funciones. No hay UI de carga
        // en SGRH todavía (necesitaría el mismo flujo de GdoEmpleadoController::storeDocumento).
        'manual_funciones',
        'observaciones', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'sgrh_area_id');
    }

    /**
     * Contratos (de cualquier estado) que tienen este cargo. "Empleados actuales de este
     * cargo" ya no es una relación directa: Empleado.cargo_id no es una columna real (se
     * deriva de contratoActivo, ver Empleado::getCargoIdAttribute()), así que no hay FK que
     * un hasMany pueda seguir. Para contar/filtrar colaboradores actuales en este cargo, se
     * usa esta relación con `->where('estado', 'Activo')` (ver CargoController).
     */
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'cargo_id');
    }

    /**
     * Cargo al que reporta directamente este cargo (para enrutar aprobaciones de permisos/
     * vacaciones en un bloque posterior).
     */
    public function jefeInmediato()
    {
        return $this->belongsTo(Cargo::class, 'jefe_inmediato_id');
    }

    /**
     * Cargo de dirección de este cargo (nivel de aprobación superior al jefe inmediato).
     */
    public function director()
    {
        return $this->belongsTo(Cargo::class, 'director_id');
    }
}
