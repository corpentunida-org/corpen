<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineaCreditoDocumento extends Model
{
    protected $table = 'cre_lineas_creditos_documentos';

    protected $fillable = [
        'cre_lineas_creditos_id',
        'descripcion',
        'orden',
    ];

    public function lineaCredito(): BelongsTo
    {
        return $this->belongsTo(LineaCredito::class, 'cre_lineas_creditos_id');
    }
}
