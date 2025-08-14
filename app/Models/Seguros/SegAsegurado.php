<?php

namespace App\Models\Seguros;

use App\Models\Maestras\maeTerceros;
use App\Models\Seguros\SegTercero;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegAsegurado extends Model
{
    use HasFactory;
    protected $table = 'SEG_asegurados';
    protected $fillable = [
        'cedula',
        'parentesco',
        'titular',
        'valorpAseguradora'
    ];

    public function tercero()
    {
        return $this->belongsTo(maeTerceros::class, 'cedula', 'cod_ter');
    }

    public function terceroAlt()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    }
    public function getNombreTerceroAttribute()
    {
        if ($this->tercero) {
            return $this->tercero->nom_ter;
        }

        if ($this->terceroAlt) {
            return $this->terceroAlt->nombre;
        }

        return 'No se encuentra registrado.';
    }

    public function terceroAF()
    {
        return $this->belongsTo(maeTerceros::class, 'cedula', 'cod_ter');
    }

    public function terceroAfAlt()
    {
        return $this->belongsTo(SegTercero::class, 'cedula', 'cedula');
    }

    public function getNombreTitularAttribute()
    {
        if ($this->tercero) {
            return $this->tercero->nom_ter;
        }

        if ($this->terceroAlt) {
            return $this->terceroAlt->nombre;
        }

        return 'No se encuentra registrado.';
    }

    public function getTerceroPreferidoAttribute()
    {
        return $this->tercero
            ?? $this->terceroAlt
            ?? $this->terceroAF
            ?? $this->terceroAfAlt
            ?? null;
    }



    public function polizas()
    {
        return $this->hasMany(SegPoliza::class, 'seg_asegurado_id', 'cedula');
    }

    public function reclamacion()
    {
        return $this->hasMany(SegReclamaciones::class, 'cedulaAsegurado', 'cedula');
    }

    public function novedades()
    {
        return $this->hasMany(SegNovedades::class, 'id_asegurado', 'cedula');
    }
}