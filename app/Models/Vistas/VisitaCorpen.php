<?php

namespace App\Models\Vistas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\maeTerceros;

class VisitaCorpen extends Model
{
    use HasFactory;

    protected $table = 'visitas_corpen';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'banco',
        'motivo',
        'fecha',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(maeTerceros::class, 'cliente_id', 'cod_ter');
    }

    // Registrar visita
    public static function registrar($cliente_id, $banco, $motivo, $usuario)
    {
        return self::create([
            'cliente_id'     => $cliente_id,
            'tipo'           => 'visita',
            'banco'          => $banco,
            'motivo'         => $motivo,
            'fecha'          => now(),
            'registrado_por' => $usuario,
        ]);
    }
}
