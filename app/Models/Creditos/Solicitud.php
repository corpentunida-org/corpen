<?php

namespace App\Models\Creditos;

use App\Models\Maestras\MaeTerceros;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'cre_legacy_solicitudes';

    public $timestamps = false;

    protected $fillable = [
        'origen',
        'num_soli',
        'fecha',
        'doc_deu',
        'tipo_cred',
        'tipo_cuota',
        'cod_congre',
        'vr_soli',
        'plazo',
        'destino',
        'ingresos',
        'egresos',
        'tiene_credito_actual',
        'cual_credito_actual',
        'ruta_formulario_firmado',
        'cod_dist',
        'protec_dato',
    ];

    protected $casts = [
        'fecha' => 'date',
        'vr_soli' => 'decimal:2',
        'tiene_credito_actual' => 'boolean',
    ];

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(MaeTerceros::class, 'doc_deu', 'cod_ter');
    }

    public function movimientosIngresos(): HasMany
    {
        return $this->hasMany(MovimientoIngreso::class, 'num_soli', 'num_soli');
    }

    public function movimientosGastos(): HasMany
    {
        return $this->hasMany(MovimientoGasto::class, 'num_soli', 'num_soli');
    }
}
