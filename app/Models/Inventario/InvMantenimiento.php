<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class InvMantenimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inv_mantenimientos'; // [cite: 533]

    protected $fillable = [
        'detalle', 'costo_mantenimiento', 'acta',
        'id_InvActivos', 'id_usersRegistro'
    ];

    public function activo() { return $this->belongsTo(InvActivo::class, 'id_InvActivos'); }
    public function creador() { return $this->belongsTo(User::class, 'id_usersRegistro'); }
}