<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etapa extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_etapas';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
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
        'descripcion',
    ];

    /**
     * Obtiene los estados asociados a esta etapa.
     * Una Etapa tiene muchos Estados.
     */
    public function estados(): HasMany
    {
        return $this->hasMany(Estado::class, 'cre_etapas_id');
    }
}