<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoGasto extends Model
{
    protected $table = 'cre_legacy_movimientos_gastos';

    public $timestamps = false;

    protected $fillable = [
        'num_soli',
        'cat_egreso_id',
        'valor_egre',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CatEgreso::class, 'cat_egreso_id');
    }
}
