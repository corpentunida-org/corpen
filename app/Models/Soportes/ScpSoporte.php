<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Maestras\maeTerceros;  

use App\Models\User;        

use App\Models\Archivo\GdoCargo;
use App\Models\Archivo\GdoArea;

use App\Models\Creditos\LineaCredito;

use App\Models\Soportes\ScpUsuario;
use App\Models\Soportes\ScpEstado;

class ScpSoporte extends Model
{
    use HasFactory;

    protected $table = 'scp_soportes';

    protected $fillable = [
        'detalles_soporte',
        'timestam',
        'id_gdo_cargo',
        'id_cre_lineas_creditos',
        'cod_ter_maeTercero', //USUARIO ASIGNADO INICIALMENTE
        'id_categoria',
        'id_scp_tipo',
        'id_scp_prioridad',
        'id_users', //USUARIO QUE CREA EL SOPORTE
        'id_scp_sub_tipo', 
        'estado', 
        'soporte', 
        'usuario_escalado',  //USUARIO ESCALADO
          
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function tipo()
    {
        return $this->belongsTo(ScpTipo::class, 'id_scp_tipo');
    }
    public function prioridad()
    {
        return $this->belongsTo(ScpPrioridad::class, 'id_scp_prioridad');
    }
    public function observaciones()
    {
        return $this->hasMany(ScpObservacion::class, 'id_scp_soporte');
    }


    public function cargo()
    {
        return $this->belongsTo(GdoCargo::class, 'id_gdo_cargo');
    }
    public function area()
    {
        return $this->hasOneThrough(
            GdoArea::class,   // Modelo destino
            GdoCargo::class,  // Modelo intermedio
            'id',          // Clave primaria en GdoCargo
            'id',          // Clave primaria en GdoArea
            'id_gdo_cargo',// Foreign key en scp_soportes
            'GDO_area_id'  // Foreign key en gdo_cargo
        );
    }




    public function lineaCredito()
    {
        return $this->belongsTo(LineaCredito::class, 'id_cre_lineas_creditos');
    }
    public function subTipo()
    {
        return $this->belongsTo(ScpSubTipo::class, 'id_scp_sub_tipo');
    }
    public function estadoSoporte()
    {
        return $this->belongsTo(ScpEstado::class, 'estado', 'id');
    }



    // Usuario que crea el soporte (tabla users)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    // Usuario asignado inicialmente (tabla mae_terceros)
    public function maeTercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter_maeTercero', 'cod_ter');
    }

    // Usuario escalado (tabla scp_usuarios)
    public function scpUsuarioAsignado()
    {
        return $this->belongsTo(ScpUsuario::class, 'usuario_escalado', 'id');
    }

    public function categoria()
    {
        return $this->belongsTo(ScpCategoria::class, 'id_categoria');
    }



}
