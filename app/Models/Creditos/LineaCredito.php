<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Interacciones\Interaction;

class LineaCredito extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_lineas_creditos';

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
        'cuenta',
        'tasa_interes',
        'plazo_minimo',
        'plazo_maximo',
        'edad_minima',
        'edad_maxima',
        'fecha_apertura',
        'fecha_cierre',
        'observacion',
        'cre_garantias_id',
        'cre_tipos_creditos_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_apertura' => 'date',
        'fecha_cierre' => 'date',
        'tasa_interes' => 'decimal:2', // Asume 2 decimales para la tasa
    ];

    /**
     * Obtiene la garantía asociada a la línea de crédito.
     */
    public function garantia(): BelongsTo
    {
        return $this->belongsTo(Garantia::class, 'cre_garantias_id');
    }

    /**
     * Obtiene el tipo de crédito asociado a la línea de crédito.
     */
    public function tipoCredito(): BelongsTo
    {
        return $this->belongsTo(TipoCredito::class, 'cre_tipos_creditos_id');
    }
    
    public function interacciones()
    {
        return $this->hasMany(Interaction::class, 'id_linea_de_obligacion', 'id');
    }


}