<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany; // <-- 


class Garantia extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_garantias';

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
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * Como no aparecen en tu diagrama, los desactivamos.
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

    public function lineasCredito(): HasMany
    {
        return $this->hasMany(LineaCredito::class, 'cre_garantias_id');
    }
}