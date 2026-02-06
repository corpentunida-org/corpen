<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvSubgrupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_subgrupos';
    protected $fillable = ['nombre', 'descripcion', 'id_InvTipos', 'id_InvLineas', 'id_InvGrupos'];

    // Relaciones Padre (BelongsTo)
    public function tipo() { return $this->belongsTo(InvTipo::class, 'id_InvTipos'); }
    public function linea() { return $this->belongsTo(InvLinea::class, 'id_InvLineas'); }
    public function grupo() { return $this->belongsTo(InvGrupo::class, 'id_InvGrupos'); }
}