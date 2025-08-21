<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- 


class TipoCredito extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_tipos_creditos';

    /**
     * La clave primaria para el modelo.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si los IDs del modelo son autoincrementables.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indica si el modelo debe tener timestamps.
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
        'nombre',
    ];

    /**
     * (NUEVA FUNCIÓN) Obtiene todas las líneas de crédito de este tipo.
     */
    public function lineasCredito(): HasMany
    {
        return $this->hasMany(LineaCredito::class, 'cre_tipos_creditos_id');
    }
}