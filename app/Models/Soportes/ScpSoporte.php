<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;   
use App\Models\User;        
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use App\Models\Soportes\ScpUsuario;

class ScpSoporte extends Model
{
    use HasFactory;

    protected $table = 'scp_soportes';

    protected $fillable = [
        'detalles_soporte',
        'timestam',
        'id_gdo_cargo',
        'id_cre_lineas_creditos',
        'cod_ter_maeTercero',
        'id_scp_tipo',
        'id_scp_prioridad',
        'id_users',
        'id_scp_sub_tipo', 
        'estado', 
        'soporte', 
        'usuario_escalado', 
          
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // Relación con ScpTipo
    public function tipo()
    {
        return $this->belongsTo(ScpTipo::class, 'id_scp_tipo');
    }

    // Relación con ScpPrioridad
    public function prioridad()
    {
        return $this->belongsTo(ScpPrioridad::class, 'id_scp_prioridad');
    }

    // Relación con MaeTerceros
    public function maeTercero()
    {
        return $this->belongsTo(maeTerceros::class, 'cod_ter_maeTercero', 'cod_ter');
    }

    // Relación con Users
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    // Relación con Observaciones
    public function observaciones()
    {
        return $this->hasMany(ScpObservacion::class, 'id_scp_soporte');
    }

    public function cargo()
    {
        return $this->belongsTo(GdoCargo::class, 'id_gdo_cargo');
    }

    public function lineaCredito()
    {
        return $this->belongsTo(LineaCredito::class, 'id_cre_lineas_creditos');
    }

    public function subTipo()
    {
        return $this->belongsTo(ScpSubTipo::class, 'id_scp_sub_tipo');
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_users_asignado');
    }
    public function scpUsuarioAsignado()
    {
        return $this->belongsTo(ScpUsuario::class, 'id_scp_usuario_asignado'); 
        // Ajusta el segundo parámetro según tu columna FK en la tabla 'scp_soportes'
    }

}
