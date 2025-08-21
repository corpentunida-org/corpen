<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagare extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_pagares';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_unico_documento',
        'cre_credito_id',
        'valor_capital',
        'tasa_interes_nominal',
        'tasa_interes_mora',
        'numero_cuotas',
        'fecha_emision',
        'fecha_vencimiento',
        'lugar_firma',
        'estado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Esto es crucial para manejar correctamente las fechas y los valores decimales.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valor_capital' => 'decimal:2',
        'tasa_interes_nominal' => 'decimal:2',
        'tasa_interes_mora' => 'decimal:2',
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    /**
     * Obtiene el crédito al que está asociado este pagaré.
     * Un Pagaré pertenece a un Crédito.
     * 
     * Nota: Se asume que existe un modelo llamado `Credito` en `app/Models/Creditos/Credito.php`.
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_credito_id');
    }
}