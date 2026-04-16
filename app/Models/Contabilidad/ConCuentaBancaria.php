<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Cartera\CarComprobantePago;

class ConCuentaBancaria extends Model
{
    /**
     * Tabla asociada al modelo.
     */
    protected $table = 'con_cuentas_bancarias';

    /**
     * Atributos asignables de forma masiva.
     * Se agrega 'estado' según el nuevo diagrama.
     */
    protected $fillable = [
        'banco',
        'numero_cuenta',
        'tipo_cuenta',
        'num_siasoft',
        'estado',  
        'convenios',
        'id_user',
    ];

    /**
     * Conversión de tipos de atributos.
     */
    protected $casts = [
        'num_siasoft'   => 'integer',
        'id_user'       => 'integer',
    ];

    /**
     * Relación: Una cuenta tiene muchos extractos.
     */
    public function extractos(): HasMany
    {
        return $this->hasMany(ConExtractoTransaccion::class, 'id_con_cuentas_bancaria');
    }
    /**
     * Relación: Una cuenta bancaria tiene muchos comprobantes de pago registrados.
     */
    public function comprobantes(): HasMany
    {
        return $this->hasMany(CarComprobantePago::class, 'id_banco', 'id');
    }
}