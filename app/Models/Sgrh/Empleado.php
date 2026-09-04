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
        'estado',
        'fecha_retiro',
        // Contacto corporativo propio del colaborador (no del cargo: un mismo cargo puede
        // tener varias personas, cada una con su propio teléfono/correo corporativo).
        'telefono_corporativo',
        'celular_corporativo',
        'ext_corporativo',
        'correo_corporativo',
        'gmail_corporativo',
        'eps',
        'arl',
        'fondo_pension',
        // Segundo fondo de pensión: preparación para la reforma pensional (Ley 2381 de
        // 2024) — un colaborador puede terminar cotizando a dos fondos a la vez.
        'fondo_pension_2',
        'tipo_sangre',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'foto_perfil',
        'observaciones',
    ];

    protected $casts = [
        'fecha_retiro' => 'date',
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
     * Cargo del catálogo sgrh_cargos que ocupa este colaborador (opcional). cargo_id ya NO es
     * columna propia — se deriva de contratoActivo (ver getCargoIdAttribute()), así que este
     * belongsTo funciona igual: internamente lee $this->cargo_id, que pasa por el accessor.
     */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    /**
     * Historial de contratos, del más reciente al más antiguo por fecha_creacion_contrato (no
     * fecha_inicio: esta última puede quedar sin definir en contratos Indefinido, y ordenar por
     * un campo que puede ser null dejaría esos registros fuera de lugar). Una renovación es el
     * mismo contrato continuado en un registro nuevo, así que el más reciente ya es siempre el
     * vigente — no hace falta forzar el estado 'Activo' aparte.
     */
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'empleado_id')
            ->latest('fecha_creacion_contrato');
    }

    /**
     * Contrato vigente del colaborador (el más reciente con estado='Activo'), si tiene uno.
     */
    public function contratoActivo()
    {
        return $this->hasOne(Contrato::class, 'empleado_id')
            ->where('estado', 'Activo')
            ->latestOfMany('fecha_creacion_contrato');
    }

    /**
     * Fecha de ingreso, cargo y salario ya NO son columnas propias de Empleado — el contrato
     * es la única fuente (no se editan directo aquí, ver EmpleadoController). Se derivan del
     * contrato activo; sin contrato activo, no hay fecha/cargo/salario "oficial" que mostrar.
     */
    public function getFechaIngresoAttribute()
    {
        return $this->contratoActivo?->fecha_inicio;
    }

    public function getCargoIdAttribute()
    {
        return $this->contratoActivo?->cargo_id;
    }

    public function getSalarioAsignadoAttribute()
    {
        return $this->contratoActivo?->salario_contrato;
    }

    public function dependientes()
    {
        return $this->hasMany(Dependiente::class, 'empleado_id')->orderBy('nombre1');
    }

    /**
     * Los estudios "en curso" (fecha_terminacion null) van primero — MySQL ordena NULL como el
     * valor más chico, así que un simple ->latest() los mandaría al final de la lista, detrás
     * de todos los ya terminados, cuando en la práctica son los más relevantes de mostrar.
     */
    public function estudios()
    {
        return $this->hasMany(Estudio::class, 'empleado_id')
            ->orderByRaw('fecha_terminacion IS NULL DESC')
            ->latest('fecha_terminacion');
    }

    public function getNombreCompletoAttribute()
    {
        if (!$this->tercero) {
            return '';
        }

        return trim("{$this->tercero->nom1} {$this->tercero->nom2} {$this->tercero->apl1} {$this->tercero->apl2}");
    }
}
