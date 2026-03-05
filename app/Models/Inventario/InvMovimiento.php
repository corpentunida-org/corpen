<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class InvMovimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_movimientos';

    // Solo dejamos los campos que el usuario o el sistema llenan manualmente
    protected $fillable = [
        'codigo_acta', 
        'acta_archivo', 
        'observacion_general',
        'id_InvTiposRegistros', 
        'id_usersAsignado', 
        'id_usersRegistro',
        'id_mantenimiento'
    ];

    public function getFile($nameFile)
    {
        $url = '#';
        if ($nameFile) {
            if (Storage::disk('s3')->exists($nameFile)) {
                $url = Storage::disk('s3')->temporaryUrl($nameFile, now()->addMinutes(5));
            }
        }
        return $url;
    }

    /**
     * Relación con el Tipo de Registro (Catálogo de Estados)
     */
    public function tipoRegistro() 
    { 
        return $this->belongsTo(InvEstado::class, 'id_InvTiposRegistros'); 
    }
    
    /**
     * Relación con el Funcionario (Quien recibe los equipos)
     */
    public function responsable() 
    { 
        return $this->belongsTo(User::class, 'id_usersAsignado'); 
    }

    /**
     * Relación con el Usuario que registra (El administrativo/técnico)
     */
    public function creador() 
    { 
        return $this->belongsTo(User::class, 'id_usersRegistro'); 
    }
    
    /**
     * Relación con los activos detallados en esta acta
     */
    public function detalles() 
    { 
        return $this->hasMany(InvMovimientoDetalle::class, 'id_InvMovimientos'); 
    }

    /**
     * Relación con el Mantenimiento asociado
     */
    public function mantenimiento() 
    { 
        return $this->belongsTo(InvMantenimiento::class, 'id_mantenimiento'); 
    }
}