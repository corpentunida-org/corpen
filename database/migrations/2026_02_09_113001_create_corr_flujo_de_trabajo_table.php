<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class FlujoDeTrabajo extends Model
{
    use HasFactory;

    protected $table = 'corr_flujo_de_trabajo';

    protected $fillable = [
        'nombre',
        'detalle',
        'usuario_id',
    ];

    /**
     * Relación: flujo pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
