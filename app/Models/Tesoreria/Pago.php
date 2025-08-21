<?php

namespace App\Models\Tesoreria;

use App\Models\Creditos\Credito;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'tes_pagos';

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
        'valor_pagado',
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
     * Obtiene el crédito al que pertenece este pago.
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_creditos_id');
    }
}