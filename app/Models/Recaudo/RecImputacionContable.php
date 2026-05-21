<?php

namespace App\Models\Recaudo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeTerceros;
use App\Models\Contabilidad\ConExtractoTransaccion;
use App\Models\Cartera\CarComprobantePago;
use App\Models\User; // <-- Asegúrate de importar el modelo User si lo usas en la cabecera

class RecImputacionContable extends Model
{
    use HasFactory;

    protected $table = 'rec_imputaciones_contables';

    protected $fillable = [
        'id',
        'id_transaccion',
        'id_tercero_origen',
        'id_distrito',
        'id_recibo',
        'tipo', 
        'concepto_contable',
        'link_ecm',
        'valor_imputado',
        'estado_conciliacion',
        'id_user', 
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

    /**
     * Relación con el Usuario (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getComprobanteAttribute()
    {
        if (!$this->id_transaccion) {
            return null;
        }

        return CarComprobantePago::with('obligacion')
            ->whereJsonContains('id_transaccion_bancaria', (string) $this->id_transaccion)
            ->orWhereJsonContains('id_transaccion_bancaria', (int) $this->id_transaccion)
            ->first();
    }
}