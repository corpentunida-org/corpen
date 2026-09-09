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
        'fecha_reafiliacion',
        'observacion_reafiliacion',
        'reafiliado_por',
        'reportado_aliado',
        'reportado_en',
        'reportado_por',
        'user_id',
    ];

    protected $casts = [
        'fecha_afiliacion' => 'date',
        'fecha_retiro' => 'date',
        'fecha_reafiliacion' => 'date',
        'reportado_aliado' => 'boolean',
        'reportado_en' => 'datetime',
    ];

    /** 'fecha_reafiliacion' nula = este retiro sigue vigente hoy. */
    public function scopeVigentes($query)
    {
        return $query->whereNull('fecha_reafiliacion');
    }

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

    public function reafiliadoPor()
    {
        return $this->belongsTo(User::class, 'reafiliado_por');
    }
}
