<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;

class ScpUsuario extends Model
{
    use HasFactory;

    protected $table = 'scp_usuarios';

    protected $fillable = [
        'cod_ter', // referencia al tercero
        'rol',     // rol dentro del sistema
    ];

    /**
     * Relación: un ScpUsuario pertenece a un MaeTercero
     */
    public function maeTercero()
    {
        return $this->belongsTo(maeTerceros::class, 'cod_ter', 'cod_ter');
    }
}
