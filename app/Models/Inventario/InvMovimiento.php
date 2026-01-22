<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class InvMovimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_movimientos'; // [cite: 471]
    
    protected $fillable = [
        'codigo_acta', 'acta_archivo', 'observacion_general',
        'id_InvTiposRegistros', 'id_usersAsignado', 'id_usersRegistro'
    ];

    // Relaciones
    // NOTA: Conectamos con InvEstado ya que dijiste que es la misma tabla lógica
    public function tipoRegistro() { return $this->belongsTo(InvEstado::class, 'id_InvTiposRegistros'); }
    
    public function responsable() { return $this->belongsTo(User::class, 'id_usersAsignado'); }
    public function creador() { return $this->belongsTo(User::class, 'id_usersRegistro'); }
    
    public function detalles() { return $this->hasMany(InvMovimientoDetalle::class, 'id_InvMovimientos'); }
}