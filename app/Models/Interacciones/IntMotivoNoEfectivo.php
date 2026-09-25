<?php

namespace App\Models\Interacciones;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntMotivoNoEfectivo extends Model
{
    use HasFactory;

    protected $table = 'int_motivos_no_efectivo';

    protected $fillable = ['id', 'name', 'area'];

    public function seguimientos()
    {
        return $this->hasMany(IntSeguimiento::class, 'motivo_no_efectivo_id');
    }
}
