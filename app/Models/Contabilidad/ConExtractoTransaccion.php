<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConExtractoTransaccion extends Model
{
    protected $table = 'con_extractos_transacciones';
    
    // Llave primaria según diagrama
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_con_cuentas_bancaria',
        'hash_transaccion',
        'fecha_movimiento',
        'referencia_cedula',     
        'referencia_nombre',     
        'valor_ingreso',
        'referencia_oficina',    
        'referencia_distrito',   
        'descripcion_banco',
        'estado_conciliacion',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'valor_ingreso'    => 'decimal:2',
    ];

    /**
     * Relación: El extracto pertenece a una cuenta bancaria.
     */
    public function cuentaBancaria(): BelongsTo
    {
        return $this->belongsTo(ConCuentaBancaria::class, 'id_con_cuentas_bancaria');
    }
}