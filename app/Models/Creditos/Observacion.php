<?php

namespace App\Models\Creditos;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observacion extends Model
{
    use HasFactory;

    /**
     * Le dice a Laravel que este modelo se conecta con la tabla 'cre_observaciones'.
     */
    protected $table = 'cre_observaciones';

    /**
     * Como tu tabla no tiene las columnas 'created_at' y 'updated_at',
     * ponemos esto en 'false' para que Laravel no las busque.
     */
    public $timestamps = false;

    /**
     * Lista de los campos de la tabla que se pueden llenar de forma masiva.
     */
    protected $fillable = [
        'asunto',
        'categoria',
        'observacion',
        'cre_creditos_id',
        'user_id',
    ];

    /**
     * Esta función crea la relación con el modelo Credito.
     * Significa que "Una Observación pertenece a un Crédito".
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_creditos_id');
    }

    /**
     * Esta función crea la relación con el modelo User.
     * Significa que "Una Observación fue creada por un Usuario".
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}