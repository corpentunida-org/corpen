<?php

namespace App\Models\Exequiales;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitularRetiro extends Model
{
    use HasFactory;

    protected $table = 'exe_titulares_retiros';

    protected $fillable = [
        'cod_cli',
        'nombre',
        'fecha_afiliacion',
        'fecha_retiro',
        'observaciones',
        'reportado_aliado',
        'reportado_en',
        'reportado_por',
        'user_id',
    ];

    protected $casts = [
        'fecha_afiliacion' => 'date',
        'fecha_retiro' => 'date',
        'reportado_aliado' => 'boolean',
        'reportado_en' => 'datetime',
    ];

    public function titular()
    {
        return $this->belongsTo(ComaeExCli::class, 'cod_cli', 'cod_cli');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reportadoPor()
    {
        return $this->belongsTo(User::class, 'reportado_por');
    }
}
