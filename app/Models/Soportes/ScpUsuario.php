<?php

namespace App\Models\Soportes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeTerceros;
use App\Models\User;

class ScpUsuario extends Model
{
    use HasFactory;

    protected $table = 'scp_usuarios';

    protected $fillable = [
        'id',
        'cod_ter', // referencia al tercero
        'usuario',
        'estado',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
    /**
     * Relación: un ScpUsuario pertenece a un MaeTercero
     */
    public function maeTercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'cod_ter', 'cod_ter');
    }

    public function UserApp()
    {
        return $this->belongsTo(User::class, 'usuario', 'id');
    }
}
