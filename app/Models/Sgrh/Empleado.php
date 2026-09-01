<?php

namespace App\Models\Sgrh;

use App\Models\Maestras\MaeTerceros;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'sgrh_empleados';

    protected $fillable = [
        'cod_ter',
        'cargo_id',
        'salario_asignado',
        'fecha_ingreso',
        'estado',
        'fecha_retiro',
        'eps',
        'arl',
        'fondo_pension',
        'tipo_sangre',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'foto_perfil',
        'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_retiro' => 'date',
        'salario_asignado' => 'decimal:2',
    ];

    /**
     * Tercero (MaeTerceros) del que este empleado toma su identificación,
     * nombre y datos de contacto. Enlace de solo lectura por ahora.
     */
    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter', 'cod_ter');
    }

    /**
     * Cargo del catálogo sgrh_cargos que ocupa este colaborador (opcional).
     */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'empleado_id')->latest('fecha_inicio');
    }

    /**
     * Contrato vigente del colaborador (el más reciente con estado='Activo'), si tiene uno.
     */
    public function contratoActivo()
    {
        return $this->hasOne(Contrato::class, 'empleado_id')
            ->where('estado', 'Activo')
            ->latestOfMany('fecha_inicio');
    }

    public function getNombreCompletoAttribute()
    {
        if (!$this->tercero) {
            return '';
        }

        return trim("{$this->tercero->nom1} {$this->tercero->nom2} {$this->tercero->apl1} {$this->tercero->apl2}");
    }
}
