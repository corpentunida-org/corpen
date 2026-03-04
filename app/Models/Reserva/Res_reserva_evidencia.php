<?php

namespace App\Models\Reserva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reserva\Res_reserva;
use App\Models\User;

class Res_reserva_evidencia extends Model
{
    use HasFactory;
    protected $fillable = ['res_reserva_id', 'description', 'user_id'];

    public function res_reserva()
    {
        return $this->belongsTo(Res_reserva::class, 'res_reserva_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
