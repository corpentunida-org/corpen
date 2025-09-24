<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maestras\claseCongregacion;
use App\Models\Maestras\maeDistritos;
use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\maeMunicipios;


class Congregacion extends Model
{
    use HasFactory;

    // --- CONFIGURACIÓN DE LA TABLA Y CLAVE PRIMARIA (YA ESTÁ BIEN) ---
    protected $table = 'MaeCongregaciones';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    // Para evitar problemas con created_at y updated_at si no las usas.
    // Si sí las tienes en tu tabla, puedes comentar o borrar esta línea.
    public $timestamps = false;


    /**
     * ¡AQUÍ ESTÁ LA CORRECCIÓN!
     * Los atributos que se pueden asignar masivamente.
     * Asegúrate de que CADA campo que quieres guardar desde un formulario esté en esta lista.
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'pastor',
        'pastorAnterior',
        'clase',
        'estado',
        'municipio',
        'direccion',
        'telefono',
        'celular',
        'distrito',
        'apertura',
        'cierre',
        'observacion',
        // 'codigo' no se incluye aquí porque es la clave primaria y no se debe cambiar en un update.
    ];

    /**
     * Define la relación con ClaseCongregacion.
     * El segundo argumento ('clase') es la clave foránea en la tabla 'Congregaciones'.
     * El tercer argumento ('id') es la clave primaria en la tabla 'claseCongregacion'.
     * Esta relación está bien configurada.
     */
    public function claseCongregacion()
    {
        return $this->belongsTo(claseCongregacion::class, 'clase', 'id');
    }


    public function maeDistritos()
    {
        return $this->belongsTo(maeDistritos::class, 'distrito', 'COD_DIST');
    }

    public function maeTerceros()
    {
        return $this->belongsTo(maeTerceros::class, 'pastor', 'cod_ter');
    }

    public function pastorAnteriorObj()
    {
        return $this->belongsTo(maeTerceros::class, 'pastorAnterior', 'cod_ter');
    }

    public function maeMunicipios()
    {
        return $this->belongsTo(maeMunicipios::class, 'municipio', 'id'); //listo
    }

}

