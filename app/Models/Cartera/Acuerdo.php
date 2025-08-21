<?php

namespace App\Models\Cartera;

use App\Enums\AcuerdoEstadoEnum; // <-- 
use App\Models\Creditos\Credito;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Acuerdo extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'car_acuerdos';

    /**
     * Indica si el modelo debe tener timestamps (created_at y updated_at).
     * El diagrama no los muestra, por lo que los desactivamos.
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
        'numero_acuerdo',
        'fecha_acuerdo',
        'estado',
        'dias_mora_inicial',
        'intereses_corrientes_acuerdo',
        'intereses_mora_acuerdo',
        'gastos_cobranza',
        'observaciones',
        'cre_creditos_id',
        'user_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_acuerdo' => 'date',
        'intereses_corrientes_acuerdo' => 'decimal:2',
        'intereses_mora_acuerdo' => 'decimal:2',
        'gastos_cobranza' => 'decimal:2',
        'estado' => AcuerdoEstadoEnum::class, // <-- ¡Aquí usamos el Enum!
    ];

    /**
     * Obtiene el crédito asociado a este acuerdo.
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_creditos_id');
    }

    /**
     * Obtiene el usuario (asesor/administrador) que registró el acuerdo.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function acuerdos(): HasMany
    {
        return $this->hasMany(Acuerdo::class, 'cre_creditos_id');
    }

}