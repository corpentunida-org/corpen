<?php

namespace App\Models\Recaudo;

use App\Models\Creditos\Credito;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoRecaudo extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'rec_pagos';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * El diagrama no los muestra, así que los desactivamos.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cuota',
        'valor_pagado',
        'rc',
        'comprobante',
        'cre_creditos_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valor_pagado' => 'double',
    ];

    /**
     * Obtiene el crédito al que se le aplicó este pago.
     * Un Pago de Recaudo pertenece a un Crédito.
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_creditos_id');
    }
}