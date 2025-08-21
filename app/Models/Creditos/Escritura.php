<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Escritura extends Model
{
    use HasFactory;

    /**
     * La tabla de la base de datos asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'cre_escrituras';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     * Es importante no incluir 'id' en esta lista.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_unico_documento',
        'cre_credito_id',
        'numero_notaria',
        'ciudad_notaria',
        'folio_matricula_inmobiliaria',
        'oficina_registro_instrumentos',
        'fecha_constitucion',
        'fecha_registro',
        'valor_gravamen',
        'estado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Esto asegura que al acceder a ellos, Laravel los trate como objetos Date o números decimales.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_constitucion' => 'date',
        'fecha_registro' => 'date',
        'valor_gravamen' => 'decimal:2',
    ];

    /**
     * Obtiene el crédito al que pertenece esta escritura.
     * Una Escritura pertenece a un Crédito.
     * 
     * Nota: Esto asume que tienes un modelo llamado `Credito` en `app/Models/Creditos/Credito.php`.
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'cre_credito_id');
    }
}