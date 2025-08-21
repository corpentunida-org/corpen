<?php

namespace App\Models\Creditos;

use App\Models\Maestras\maeTerceros; // <-- Asumiendo la ubicación de este modelo
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Creditos\Notificacion;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Credito extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_creditos';

    /**
     * El nombre de la clave primaria. El diagrama muestra 'Id' pero Laravel por defecto
     * y por convención usa 'id' en minúscula. Lo dejamos así si la migración usa ->id().
     *
     * @var string
     */
    // protected $primaryKey = 'Id'; // Descomentar solo si la columna es realmente 'Id' con mayúscula

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
        'pr',
        'pagare',
        'valor',
        'cuotas',
        'fecha_desembolso',
        'cre_estados_id',
        'mae_terceros_cedula',
        'cre_lineas_creditos_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valor' => 'double',
        'fecha_desembolso' => 'date',
        'acuerdo' => 'boolean',
    ];

    // --- RELACIONES "PERTENECE A" (BELONGS TO) ---

    /**
     * Obtiene el estado del crédito.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'cre_estados_id');
    }

    /**
     * Obtiene la línea de crédito asociada.
     */
    public function lineaCredito(): BelongsTo
    {
        return $this->belongsTo(LineaCredito::class, 'cre_lineas_creditos_id');
    }

    /**
     * Obtiene el tercero (cliente) asociado al crédito.
     * Nota: Se especifica la llave foránea y la llave del dueño (owner key)
     * porque no siguen la convención estándar de Laravel (ej: tercero_id).
     */
    public function tercero(): BelongsTo
    {
        return $this->belongsTo(maeTerceros::class, 'mae_terceros_cedula', 'cedula');
    }


    // --- RELACIONES "TIENE UN/A" (HAS ONE) ---

    /**
     * Obtiene el pagaré asociado a este crédito.
     */
    public function pagareRelacionado(): HasOne
    {
        return $this->hasOne(Pagare::class, 'cre_credito_id');
    }

    /**
     * Obtiene la escritura asociada a este crédito.
     */
    public function escritura(): HasOne
    {
        return $this->hasOne(Escritura::class, 'cre_credito_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'cre_creditos_id');
    } 
}