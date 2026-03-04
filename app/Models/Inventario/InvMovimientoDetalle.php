<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class InvMovimientoDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_movimientos_detalle';

    // Limpiamos los IDs automáticos y timestamps para un código más estándar
    protected $fillable = [
        'estado_individual',
        'id_InvMovimientos', 
        'id_InvActivos',
        'id_estado',
        'id_usersDelActivo'
    ];

    /**
     * Relación: Regresa al acta principal (Cabecera)
     */
    public function movimiento() 
    { 
        return $this->belongsTo(InvMovimiento::class, 'id_InvMovimientos'); 
    }

    /**
     * Relación: El equipo físico involucrado
     */
    public function activo() 
    { 
        return $this->belongsTo(InvActivo::class, 'id_InvActivos'); 
    }

    /**
     * Relación: El usuario que se hace cargo (o entrega) este activo específico
     */
    public function usuario() 
    { 
        return $this->belongsTo(User::class, 'id_usersDelActivo'); 
    }

    /**
     * Relación: El estado exacto asignado en este detalle
     */
    public function estado()
    {
        return $this->belongsTo(InvEstado::class, 'id_estado');
    }
}