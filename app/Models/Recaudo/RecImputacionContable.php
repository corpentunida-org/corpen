<?php

namespace App\Models\Recaudo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeTerceros;
use App\Models\Contabilidad\ConExtractoTransaccion;

class RecImputacionContable extends Model
{
    use HasFactory;

    protected $table = 'rec_imputaciones_contables';

    protected $fillable = [
        'id_transaccion',
        'id_tercero_origen',
        'id_distrito',
        'id_recibo',
        'concepto_contable',
        'link_ecm',
        'valor_imputado',
        'estado_conciliacion',
    ];

    /**
     * Relación con el Extracto Bancario
     */
    public function transaccion()
    {
        return $this->belongsTo(ConExtractoTransaccion::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Relación con el Tercero (MaeTerceros)
     */
    public function tercero()
    {
        return $this->belongsTo(MaeTerceros::class, 'id_tercero_origen', 'cod_ter');
    }

    /**
     * Relación con el Distrito (MaeDistritos)
     */
    public function distrito()
    {
        return $this->belongsTo(MaeDistritos::class, 'id_distrito', 'COD_DIST');
    }
}