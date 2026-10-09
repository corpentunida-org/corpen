<?php

namespace App\Models\Rsv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInmueble extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada al modelo.
     * Es necesario declararlo explícitamente porque no sigue el plural estándar de inglés.
     *
     * @var string
     */
    protected $table = 'rsv_tipo_inmueble';

    /**
     * Los atributos que son asignables masivamente (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'active',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación: Un Tipo de Inmueble tiene muchos Inmuebles en el catálogo.
     */
    public function catalogoInmuebles()
    {
        // Se asume que el modelo CatalogoInmueble está en el mismo namespace
        return $this->hasMany(CatalogoInmueble::class, 'tipo_inmueble_id');
    }
}
