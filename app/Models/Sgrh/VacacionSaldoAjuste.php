<?php

namespace App\Models\Sgrh;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VacacionSaldoAjuste extends Model
{
    protected $table = 'sgrh_vacacion_saldo_ajustes';

    protected $fillable = [
        'empleado_id',
        'dias',
        'motivo',
        'user_id',
    ];

    protected $casts = [
        'dias' => 'decimal:2',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
