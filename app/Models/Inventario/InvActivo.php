<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
// Si tienes modelo de Municipios, impórtalo: use App\Models\MaeMunicipio;

class InvActivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_activos'; // [cite: 415]
    
    protected $fillable = [
        'nombre', 'codigo_activo', 'serial', 'descripcion', 
        'unidad_medida', 'hoja_vida',
        'fecha_inicio_garantia', 'fecha_fin_garantia', 'vida_util_meses',
        // Claves Foráneas [cite: 426-446]
        'id_InvSubGrupos', 'id_InvMarcas', 'id_InvBodegas',
        'id_MaeMunicipios', 'id_InvDetalleCompras', 
        'id_usersRegistro', 'id_Estado', 'id_ultimo_usuario_asignado'
    ];

    // Relaciones - Clasificación
    public function subgrupo() { return $this->belongsTo(InvSubgrupo::class, 'id_InvSubGrupos'); }
    public function marca() { return $this->belongsTo(InvMarca::class, 'id_InvMarcas'); }
    public function bodega() { return $this->belongsTo(InvBodega::class, 'id_InvBodegas'); }
    public function estado() { return $this->belongsTo(InvEstado::class, 'id_Estado'); }
    
    // Relaciones - Origen
    public function detalleCompra() { return $this->belongsTo(InvDetalleCompra::class, 'id_InvDetalleCompras'); }
    
    // Relaciones - Personas
    public function usuarioRegistro() { return $this->belongsTo(User::class, 'id_usersRegistro'); }
    public function usuarioAsignado() { return $this->belongsTo(User::class, 'id_ultimo_usuario_asignado'); }
}