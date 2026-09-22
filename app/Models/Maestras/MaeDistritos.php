<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeCongregacion;
use App\Models\Maestras\MaeTerceros;

class MaeDistritos extends Model
{
    use HasFactory;

    protected $table = 'MaeDistritos';

    // 👇 AGREGAR ESTO URGENTE 👇
    protected $primaryKey = 'COD_DIST';
    public $incrementing = false;
    protected $keyType = 'string';
    // 👆 ======================= 👆

    public $timestamps = false;

    protected $fillable = [
        'COD_DIST',
        'NOM_DIST',
        'DETALLE',
        'COMPUEST',
        'cc_supervisor',
        'cc_primer_presb',
        'cc_segundo_presb',
        'cc_tercer_presb',
        'cc_secre_presb',
        'cc_teso_presb',
        'cc_fiscal',
        'cc_asesor_corpen',
    ];

    public function congregaciones()
    {
        return $this->hasMany(MaeCongregacion::class, 'distrito', 'COD_DIST');
    }

    public function terceros()
    {
        return $this->hasMany(MaeTerceros::class, 'cod_dist', 'COD_DIST');
    }

    public function supervisor()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_supervisor', 'cod_ter');
    }

    public function primerPresbitero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_primer_presb', 'cod_ter');
    }

    public function segundoPresbitero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_segundo_presb', 'cod_ter');
    }

    public function tercerPresbitero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_tercer_presb', 'cod_ter');
    }

    public function secretarioPresbiterio()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_secre_presb', 'cod_ter');
    }

    public function tesoreroPresbiterio()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_teso_presb', 'cod_ter');
    }

    public function fiscal()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_fiscal', 'cod_ter');
    }

    public function asesorCorpen()
    {
        return $this->belongsTo(MaeTerceros::class, 'cc_asesor_corpen', 'cod_ter');
    }
}
