<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User; // Importamos User

class InvCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_compras'; // [cite: 357]
    protected $fillable = [
        'numero_factura', 'fecha_factura', 'total_pago', 
        'num_doc_interno', 'numero_egreso', 'eg_archivo',
        'id_InvMetodos', 'id_usersRegistro'
    ];

    // Relaciones
    public function metodo() { return $this->belongsTo(InvMetodo::class, 'id_InvMetodos'); }
    public function usuarioRegistro() { return $this->belongsTo(User::class, 'id_usersRegistro'); }
    public function detalles() { return $this->hasMany(InvDetalleCompra::class, 'id_InvCompras'); }
}