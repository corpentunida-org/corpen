<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Maestras\Congregacion;
use App\Models\Vistas\VisitaCorpen;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\maeDistritos;
use App\Models\Soportes\ScpUsuario;
use App\Models\Interacciones\Interaction;


class maeTerceros extends Model
{
    use HasFactory;

    protected $table = 'MaeTerceros';

    protected $primaryKey = 'cod_ter';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $casts = [
        'fec_minis' => 'datetime',
        'fecha_ipuc' => 'datetime',
        'fec_aport' => 'datetime',
        'fec_nac' => 'datetime',
    ];

    protected $fillable = [

        // ----------------------
        // IDENTIFICACIÓN TERCERO
        // ----------------------
        'id',               // ID interno (PK)
        'id_ter',           // ID tercero externo
        'cod_ter',          // CÉDULA O NIT
        'dv',               // DÍGITO DE VERIFICACIÓN
        'digito_v',         // DÍGITO VERIFICACIÓN alternativo
        'tdoc',             // TIPO DOCUMENTO
        'tip_pers',         // TIPO DE PERSONA (natural o jurídica)
        'tipo_ter',         // TIPO DE TERCERO
        'tip_cli',          // TIPO DE CLIENTE

        // ----------------------
        // ACTIVIDAD / CLASIFICACIÓN
        // ----------------------
        'cod_activ',        // CÓDIGO ACTIVIDAD
        'Cod_acteco',       // CÓDIGO ACTIVIDAD ECONÓMICA
        'cod_cla',          // CÓDIGO CLASIFICACIÓN
        'clasific',         // CLASIFICACIÓN
        'clas_cli',         // CLASIFICACIÓN CLIENTE

        // ----------------------
        // UBICACIÓN / CÓDIGOS
        // ----------------------
        'cod_ciu',          // CÓDIGO CIUDAD
        'ciudad',           // CIUDAD
        'mun',              // MUNICIPIO
        'dpto',             // DEPARTAMENTO
        'depa',             // DEPARTAMENTO alternativo
        'pais',             // PAÍS
        'cod_depa',         // CÓDIGO DEPARTAMENTO
        'cod_pais',         // CÓDIGO PAÍS
        'cod_dist',         // CÓDIGO DISTRITO
        'cod_postal',       // CÓDIGO POSTAL
        'codpostal',        // CÓDIGO POSTAL alternativo
        'cod_suc',          // CÓDIGO SUCURSAL
        'cod_bod',          // CÓDIGO BODEGA
        'cod_ban',          // CÓDIGO BANCO
        'cod_zona',         // CÓDIGO ZONA
        'cod_ven',          // CÓDIGO VENDEDOR
        'cod_ven1',         // CÓDIGO VENDEDOR 1
        'cod_ven2',         // CÓDIGO VENDEDOR 2
        'cod_ven3',         // CÓDIGO VENDEDOR 3
        'cod_ven4',         // CÓDIGO VENDEDOR 4
        'cod_lice',         // CÓDIGO LICENCIA
        'cod_clase',        // CÓDIGO CLASE
        'cod_est',          // CÓDIGO ESTADO
        'cod_respfiscal',   // CÓDIGO RESPONSABLE FISCAL
        'cod_tributo',      // CÓDIGO TRIBUTO

        // ----------------------
        // DATOS PERSONALES
        // ----------------------
        'nom_ter',          // NOMBRE DEL TERCERO
        'apl1', 'apl2',     // APELLIDOS
        'nom1', 'nom2',     // NOMBRES
        'raz',              // RAZÓN SOCIAL
        'razon_soc',        // RAZÓN SOCIAL alternativo
        'repres',           // REPRESENTANTE
        'sexo',             // SEXO
        'lugar_naci',       // LUGAR DE NACIMIENTO
        'fec_nac',          // FECHA DE NACIMIENTO
        'est_civil',        // ESTADO CIVIL

        // ----------------------
        // CONYUGE / FAMILIA
        // ----------------------
        'id_conyuge',       // ID CÓNYUGE
        'nom_conyug',       // NOMBRE CÓNYUGE
        'mail_conyu',       // EMAIL CÓNYUGE
        'num_hijos',        // NÚMERO DE HIJOS
        'parentesco',       // PARENTESCO

        // ----------------------
        // CONTACTO
        // ----------------------
        'tel',              // TELÉFONO
        'tel1',             // TELÉFONO 1
        'tel2',             // TELÉFONO 2
        'cel',              // CELULAR
        'fax1',             // FAX
        'email',            // CORREO
        'email_fac',        // EMAIL FACTURACIÓN
        'email_fact',       // EMAIL FACTURA
        'email_fe',         // EMAIL FACTURACIÓN ELECTRÓNICA
        'contacto',         // CONTACTO
        'cont_cxc',         // CONTACTO CXC
        'cont_tel',         // TELÉFONO CONTACTO

        // ----------------------
        // DOMICILIO
        // ----------------------
        'dir', 'dir1', 'dir2', // DIRECCIONES
        'dir_comer',           // DIRECCIÓN COMERCIAL
        'ciu_comer',           // CIUDAD COMERCIAL
        'barrio',              // BARRIO
        'exten',               // EXTENSIÓN

        // ----------------------
        // INFORMACIÓN COMERCIAL / CLIENTE
        // ----------------------
        'tip_prv',             // TIPO TERCERO
        'ind_cred',            // INDICADOR CRÉDITO
        'cupo_cred',           // CUPO CRÉDITO
        'ind_rete',            // INDICADOR RETENCIÓN
        'aut_ret',             // AUTORIZACIÓN RETENCIÓN
        'ind_iva',             // INDICADOR IVA
        'ind_cree',            // INDICADOR CREE
        'ind_requ',            // INDICADOR REQUISITOS
        'ind_items',           // INDICADOR ITEMS
        'ind_doc',             // INDICADOR DOCUMENTO
        'ind_tmk',             // INDICADOR TMK
        'indpcom',             // INDICADOR PAGO COMISIÓN
        'por_com', 'por_comi', // PORCENTAJE COMISIÓN
        'pc1', 'pc2', 'pc3',   // PORCENTAJE COMISIÓN DETALLADO
        'dp1', 'dp2', 'dp3',   // DESCUENTOS DETALLADOS
        'dto_det',             // DESCUENTO DETALLADO
        'por_des',             // PORCENTAJE DESCUENTO
        'prec_rem',            // PRECIO REMISIÓN
        'lista_prec',          // LISTA DE PRECIO
        'pla_com',             // PLAZO COMERCIAL
        'dia_plaz', 'dia_com', 'dia_adp', // DÍAS
        'ind_suc',             // INDICADOR SUCURSAL
        'suc_cli',             // SUCURSAL CLIENTE
        'cod_can',             // CÓDIGO CANAL
        'esp_gab',             // ESPECIAL GABINETE
        'uni_fra',             // UNIDAD FRACCIÓN
        'ind_mayor',           // INDICADOR MAYORISTA
        'r_semana',            // RANGO SEMANAL
        'pago', 'pago1',       // FORMA DE PAGO

        // ----------------------
        // INFORMACIÓN FINANCIERA
        // ----------------------
        'cupo_cxc',            // CUPO CXC
        'i_cupocc',            // ÍNDICE CUPO CXC
        'cupo_cxp',            // CUPO CXP
        'i_cupocp',            // ÍNDICE CUPO CXP
        'cta',                 // CUENTA
        'cta_ban',             // CUENTA BANCARIA
        'cta_icap',            // CUENTA ICAP
        'cta_icac',            // CUENTA ICAC
        'por_cred',            // PORCENTAJE CRÉDITO
        'int_mora',            // INTERÉS MORATORIO
        'icrecon',             // ÍNDICE RECONOCIMIENTO

        // ----------------------
        // FECHAS IMPORTANTES
        // ----------------------
        'fec_ing', 'fec_cump', 'fec_act', 'fec_dat',
        'fec_falle', 'fecha_lice', 'fecha_ipuc',
        'fec_aport', 'fec_expcc', 'fecha_aded', 'fec_minis',

        // ----------------------
        // IMPUESTOS / RETENCIONES
        // ----------------------
        'regimen', 'codimpuesto', 'ret_prv', 'bloqueo', 
        'bloq_aut', 'bloq_tmk', 'bloq_ate', 'exo_bloq', 
        'ret_iva', 'rtiva', 'ret_ica', 'rtica',

        // ----------------------
        // OTROS
        // ----------------------
        'cargo', 'congrega', 'conta', 'inf_ter', 
        'matricula', 'observ', 'lugar_expcc', 'respon', 'por_ica',
    ];


    public function getEdadAttribute()
    {
        if ($this->fec_nac) {
            return Carbon::parse($this->fec_nac)->age;
        }
        return null;
    }

    public function getGeneroAttribute()
    {
        if ($this->sexo) {
            return $this->sexo === 'V' ? 'Masculino' : 'Femenino';
        }
        return null;
    }

    /**
     * Relación uno a muchos con Congregaciones
     * Un tercero puede estar relacionado con muchas congregaciones
     */
    public function congregaciones()
    {
        return $this->hasOne(Congregacion::class, 'codigo', 'congrega');
    }



    public function maeTipos()
    {
        return $this->belongsTo(MaeTipo::class, 'tip_prv', 'id');
    }

    //RELACION INTERACCION
    public function interactions()
    {
        return $this->hasMany(Interaction::class, 'client_id');
    }
    //RELACION VISITAS
    public function visitasCorpen()
    {
        return $this->hasMany(VisitaCorpen::class, 'cliente_id', 'cod_ter');
    }

    public function scpUsuarios()
    {
        return $this->hasMany(ScpUsuario::class, 'cod_ter', 'cod_ter');
    }

    public function distrito()
    {
        return $this->belongsTo(maeDistritos::class, 'cod_dist', 'COD_DIST');
    }

    //SECCION FLUJO DE SOLICITUDES
    // Correspondencias enviadas por este tercero
    public function correspondencias()
    {
        return $this->hasMany(\App\Models\Correspondencia\Correspondencia::class, 'remitente_id', 'cod_ter');
    }
}
