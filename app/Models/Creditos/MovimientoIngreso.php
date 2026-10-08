<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoIngreso extends Model
{
    protected $table = 'cre_legacy_movimientos_ingresos';

    public $timestamps = false;

    protected $fillable = [
        'num_soli',
        'cat_ingreso_id',
        'valor_ing',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CatIngreso::class, 'cat_ingreso_id');
    }
}
