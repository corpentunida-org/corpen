<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Maestras\MaeTerceros;

class InvCompra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_compras';

    protected $fillable = [
        'numero_factura',
        'fecha_factura',
        'total_pago',
        'num_doc_interno',
        'numero_egreso',
        'eg_archivo',
        'id_InvMetodos',
        'id_usersRegistro',
        'cod_ter_proveedor', // Campo agregado
    ];

    // Mutadores de atributos (opcional, pero recomendado)
    protected $casts = [
        'fecha_factura' => 'date',
        'total_pago' => 'decimal:2',
    ];

    // Relaciones
    public function metodo()
    {
        return $this->belongsTo(InvMetodo::class, 'id_InvMetodos');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'id_usersRegistro');
    }

    public function detalles()
    {
        return $this->hasMany(InvDetalleCompra::class, 'id_InvCompras');
    }

    public function proveedor()
    {
        // belongsTo(Modelo Relacionado, llave_foranea, llave_primaria_del_modelo_relacionado)
        return $this->belongsTo(MaeTerceros::class, 'cod_ter_proveedor', 'cod_ter');
    }
}
