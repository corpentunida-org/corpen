<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\Congregacion;
use App\Models\Maestras\MaeTipo;

class maeTerceros extends Model
{
    use HasFactory;
    
    protected $table = 'MaeTerceros';

    protected $primaryKey = 'cod_ter';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // <- esto evita el error con updated_at


    protected $fillable = [
        'id',
        'cod_ter', 
        'nom_ter', 
        'estado', 
        'apl1', 
        'apl2', 
        'nom1', 
        'nom2', 
        'dir', 
        'dir1', 
        'dir2',
        'tel1', 
        'email', 
        'fax1', 
        'fec_ing', 
        'ciudad', 
        'tip_prv', 
        'cod_act', 
        'cod_cla',
        'int_mora', 
        'dia_plaz', 
        'por_des', 
        'observ', 
        'aut_ret', 
        'por_ica', 
        'repres',
        'cta_ban', 
        'clasific', 
        'cod_can', 
        'cod_ven', 
        'por_com', 
        'ind_suc', 
        'ind_cred',
        'cupo_cred', 
        'ind_rete', 
        'raz', 
        'dpto', 
        'mun', 
        'tip_pers', 
        'dv', 
        'tdoc', 
        'cod_ciu',
        'cont_cxc', 
        'ind_ret', 
        'i_cupocc', 
        'cupo_cxc', 
        'i_cupocp', 
        'cod_zona', 
        'bloqueo',
        'exten', 
        'depa', 
        'conta', 
        'cargo', 
        'cel',
        'rtiva', 
        'rtica', 
        'pais', 
        'prec_rem',
        'lista_prec', 
        'ind_iva', 
        'cupo_cxp', 
        'id_ter', 
        'cod_bod', 
        'ind_mayor', 
        'fec_cump',
        'tel2', 
        'por_cred', 
        'pla_com', 
        'r_semana', 
        'pago', 
        'pago1', 
        'tipo_ter', 
        'razon_soc',
        'apell1', 
        'apell2', 
        'digito_v', 
        'dir_comer', 
        'ret_iva', 
        'ret_ica', 
        'bloq_aut',
        'bloq_tmk', 
        'bloq_ate', 
        'cod_ban', 
        'cta', 
        'cod_depa', 
        'cod_pais', 
        'uni_fra',
        'ind_requ', 
        'XXX', 
        'ind_items', 
        'fec_act', 
        'icrecon', 
        'ind_doc', 
        'dia_com',
        'esp_gab', 
        'ret_prv', 
        'inf_ter', 
        'indpcom', 
        'dp1', 
        'dp2', 
        'dp3', 
        'pc1', 
        'pc2',
        'pc3', 
        'por_comi', 
        'ind_tmk', 
        'fec_dat', 
        'email_fe', 
        'dto_det', 
        'ciu_comer',
        'exo_bloq', 
        'tip_cli', 
        'dia_adp', 
        'clas_cli', 
        'fec_nac', 
        'contacto', 
        'cont_tel',
        'i_puntos', 
        'ind_cree', 
        'cod_activ', 
        'cod_ven1', 
        'cod_ven2', 
        'cod_ven3', 
        'cod_ven4',
        'email_fact', 
        'cod_suc', 
        'fecha_aded', 
        'suc_cli', 
        'cod_respfiscal', 
        'cod_tributo',
        'cod_postal', 
        'Cod_acteco', 
        'fec_minis', 
        'cod_dist', 
        'cod_est', 
        'tel',
        'mail_conyu', 
        'num_hijos', 
        'parentesco', 
        'nom_conyug', 
        'id_conyuge', 
        'est_civil',
        'barrio', 
        'fec_falle', 
        'fecha_lice', 
        'fecha_ipuc', 
        'sexo', 
        'lugar_naci',
        'cod_lice', 
        'cod_clase', 
        'congrega', 
        'fec_aport', 
        'fec_expcc', 
        'lugar_expcc',
        'respon', 
        'regimen', 
        'matricula', 
        'codimpuesto', 
        'codpostal', 
        'cta_icap',
        'cta_icac', 
        'email_fac',
    ];

    /**
     * Relación uno a muchos con Congregaciones
     * Un tercero puede estar relacionado con muchas congregaciones
     */
    public function congregaciones()
    {
        return $this->hasMany(Congregacion::class, 'cod_ter', 'pastor');
    }

    public function maeTipos()
    {
        return $this->belongsTo(MaeTipo::class, 'tip_prv', 'codigo');
    }
    
}
