<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelos Relacionados
use App\Models\Maestras\MaeMunicipio;
use App\Models\Maestras\MaeMunicipios;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvBodega;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvReferencia;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\User;

class InvActivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_activos';
    
    protected $fillable = [
        'id', // ID del sistema
        'nombre', // Arear de Sistemas
        'codigo_activo', // Arear de Sistemas
        'serial', // Arear de Sistemas
        'descripcion', // Arear de Sistemas
        'unidad_medida', // Arear de Sistemas
        'hoja_vida',// Arear de Sistemas
        'fecha_inicio_garantia', /* INSERCION - FECHA DEL CAMPO (Modelo: class InvCompra extends Model) - (Campo: "fecha_factura") */ 
        'fecha_fin_garantia', // Arear de Sistemas
        'vida_util_meses',// Arear de Sistemas
        // Claves Foráneas
        'id_InvSubGrupos', // Arear de Sistemas
        'id_InvMarcas', // Arear de Sistemas
        'id_InvBodegas',// Arear de Sistemas
        'id_MaeMunicipios', // Arear de Sistemas
        'id_InvDetalleCompras', /* INSERCION - ID (Modelo: class InvDetalleCompra extends Model) - (Campo: "id") */
        'id_usersRegistro', /* INSERCION - CAMPO (Modelo: class InvCompra extends Model) - (Campo: "id_usersRegistro") */
        'id_Estado', // Arear de Sistemas
        'id_ultimo_usuario_asignado',// Arear de Sistemas
        'invReferencias_id' /* INSERCION - CAMPO (class InvDetalleCompra extends Model) - (Campo: "invReferencias_id") */
    ];

    protected $casts = [
        'fecha_inicio_garantia' => 'date',
        'fecha_fin_garantia'    => 'date',
        'vida_util_meses'       => 'integer',
    ];

    // --- RELACIONES ---

    // Clasificación y Referencia
    public function subgrupo() { return $this->belongsTo(InvSubgrupo::class, 'id_InvSubGrupos'); }
    public function marca() { return $this->belongsTo(InvMarca::class, 'id_InvMarcas'); }
    public function bodega() { return $this->belongsTo(InvBodega::class, 'id_InvBodegas'); }
    public function estado() { return $this->belongsTo(InvEstado::class, 'id_Estado'); }
    public function referencia() { return $this->belongsTo(InvReferencia::class, 'invReferencias_id'); }
    
    // Ubicación Geográfica
    public function municipio() { return $this->belongsTo(MaeMunicipios::class, 'id_MaeMunicipios'); }

    // Origen de Compra
    public function detalleCompra() { return $this->belongsTo(InvDetalleCompra::class, 'id_InvDetalleCompras'); }
    
    // Usuarios Relacionados
    public function usuarioRegistro() { return $this->belongsTo(User::class, 'id_usersRegistro'); }
    public function usuarioAsignado() { return $this->belongsTo(User::class, 'id_ultimo_usuario_asignado'); }
}