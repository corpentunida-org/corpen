<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Modelos Relacionados
use App\Models\Maestras\MaeMunicipios;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvReferencia;
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvBodega;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvMovimiento;
use App\Models\Inventario\InvMantenimiento;
use App\Models\User;

class InvActivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_activos';
    
    protected $fillable = [
        'id', 
        'nombre', 
        'codigo_activo', 
        'serial', 
        'descripcion', 
        'unidad_medida', 
        'hoja_vida',
        'fecha_inicio_garantia',
        'fecha_fin_garantia', 
        'vida_util_meses',
        // Claves Foráneas Directas
        'id_MaeMunicipios', 
        'id_InvDetalleCompras', 
        'id_usersRegistro', 
        'id_Estado', 
        'id_ultimo_usuario_asignado',
        'invReferencias_id' // El puente principal
    ];

    protected $casts = [
        'fecha_inicio_garantia' => 'date',
        'fecha_fin_garantia'    => 'date',
        'vida_util_meses'       => 'integer',
    ];

    // --- RELACIONES DIRECTAS ---

    public function estado() 
    { 
        return $this->belongsTo(InvEstado::class, 'id_Estado'); 
    }
    
    public function referencia() 
    { 
        return $this->belongsTo(InvReferencia::class, 'invReferencias_id'); 
    }
    
    public function municipio() 
    { 
        return $this->belongsTo(MaeMunicipios::class, 'id_MaeMunicipios'); 
    }

    public function detalleCompra() 
    { 
        return $this->belongsTo(InvDetalleCompra::class, 'id_InvDetalleCompras'); 
    }
    
    public function usuarioRegistro() 
    { 
        return $this->belongsTo(User::class, 'id_usersRegistro'); 
    }
    
    public function usuarioAsignado() 
    { 
        return $this->belongsTo(User::class, 'id_ultimo_usuario_asignado'); 
    }

    // --- RELACIONES INDIRECTAS (Vía Referencia) ---
    // Estas relaciones permiten usar ->with(['marca', 'subgrupo', 'bodega']) en el controlador

    /**
     * Obtener la marca a través de la referencia.
     */
    public function marca()
    {
        return $this->hasOneThrough(
            InvMarca::class,      // Modelo destino
            InvReferencia::class, // Modelo intermedio
            'id',                // FK en inv_referencias (id de la referencia)
            'id',                // FK en inv_marcas (id de la marca)
            'invReferencias_id', // Local key en inv_activos
            'id_InvMarcas'       // Local key en inv_referencias
        );
    }

    /**
     * Obtener el subgrupo a través de la referencia.
     * Esto soluciona el error de "Undefined relationship [subgrupo]"
     */
    public function subgrupo()
    {
        return $this->hasOneThrough(
            InvSubgrupo::class,
            InvReferencia::class,
            'id',                // FK en inv_referencias
            'id',                // FK en inv_subgrupos
            'invReferencias_id', // Local key en inv_activos
            'id_InvSubGrupos'    // Local key en inv_referencias
        );
    }

    /**
     * Obtener la bodega a través de la referencia.
     */
    public function bodega()
    {
        return $this->hasOneThrough(
            InvBodega::class,
            InvReferencia::class,
            'id',                // FK en inv_referencias
            'id',                // FK en inv_bodegas
            'invReferencias_id', // Local key en inv_activos
            'id_InvBodegas'      // Local key en inv_referencias
        );
    }

    // --- RELACIONES DE OPERACIÓN ---

    public function movimientos()
    {
        return $this->belongsToMany(
            InvMovimiento::class,
            'inv_movimientos_detalle',
            'id_InvActivos',
            'id_InvMovimientos'
        );
    }

    public function mantenimientos()
    {
        return $this->hasMany(InvMantenimiento::class, 'id_InvActivos');
    }
}