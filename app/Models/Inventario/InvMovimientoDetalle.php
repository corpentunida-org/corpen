<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class InvMovimientoDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_movimientos_detalle'; // [cite: 500]

    protected $fillable = [
        'estado_individual',
        'id_InvMovimientos', 'id_InvActivos', 
        'id_InvTiposRegistros', 'id_usersDelActivo'
    ];

    public function movimiento() { return $this->belongsTo(InvMovimiento::class, 'id_InvMovimientos'); }
    public function activo() { return $this->belongsTo(InvActivo::class, 'id_InvActivos'); }
    public function usuario() { return $this->belongsTo(User::class, 'id_usersDelActivo'); }
}