<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvTipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_tipos'; // [cite: 256]
    protected $fillable = ['nombre', 'descripcion'];

    public function subgrupos()
    {
        return $this->hasMany(InvSubgrupo::class, 'id_InvTipos');
    }
}