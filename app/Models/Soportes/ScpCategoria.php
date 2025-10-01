<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScpCategoria extends Model
{
    use HasFactory;

    protected $table = 'scp_categorias';

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
    ];

    // Relación con tipos
    public function tipos()
    {
        return $this->hasMany(ScpTipo::class, 'id_categoria');
    }
}
