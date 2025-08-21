<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- 


class TipoDocumento extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_tipo_documentos';

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
     * Obtiene la etapa a la que pertenece este tipo de documento.
     * Un Tipo de Documento pertenece a una Etapa.
     */
    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class, 'cre_etapas_id');
    }
    /**
     * (NUEVA FUNCIÓN) Obtiene todos los documentos de este tipo.
     * Un Tipo de Documento puede tener muchos Documentos.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'cre_tipo_documentos_id');
    }

}