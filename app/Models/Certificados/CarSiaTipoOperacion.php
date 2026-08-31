<?php


namespace App\Models\Certificados;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;


class CarSiaTipoOperacion extends Model
{
    use HasFactory, SoftDeletes;


    // 1. Especificar la tabla exacta
    protected $table = 'car_sia_tipos_operacion';


    // 2. Campos asignables masivamente
    protected $fillable = [
        'id_car_sia_operaciones', //null - En inyectarBloques queda blanco
        'id_car_sia_tipos',
        'numero_bloque', // En inyectarBloques
        'id_user',
    ];


    // 3. Relaciones


    // Puede pertenecer a una operación específica (es nullable)
    public function operacion()
    {
        return $this->belongsTo(CarSiaOperacion::class, 'id_car_sia_operaciones');
    }


    // Pertenece obligatoriamente a un tipo del catálogo (tipología)
    public function tipo()
    {
        return $this->belongsTo(CarSiaTipo::class, 'id_car_sia_tipos');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}


