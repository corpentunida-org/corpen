<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- 


class Estado extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_estados';

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
        'cre_etapas_id',
    ];

    /**
     * Obtiene la etapa a la que pertenece este estado.
     * Un Estado pertenece a una Etapa.
     */
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class, 'cre_etapas_id');
    }
    
    public function creditos(): HasMany
    {
        return $this->hasMany(Credito::class, 'cre_estados_id');
    }
}