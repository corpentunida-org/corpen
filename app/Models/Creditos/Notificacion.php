<?php

namespace App\Models\Creditos;

// Importamos el Enum que acabamos de crear para poder usarlo.
use App\Enums\NotificacionEstadoEnum;
use App\Models\Maestras\maeTerceros; // <-- 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
use HasFactory;

/**
* Le dice a Laravel que este modelo se conecta con la tabla 'cre_notificaciones'.
*/
protected $table = 'cre_notificaciones';

/**
* Como tu tabla no tiene las columnas 'created_at' y 'updated_at',
* ponemos esto en 'false' para que Laravel no las busque.
*/
public $timestamps = false;

/**
* Lista de los campos de la tabla que se pueden llenar de forma masiva.
* Es una medida de seguridad de Laravel.
*/
protected $fillable = [
'asunto',
'mensaje',
'canal',
'estado',
'cre_creditos_id',
'mae_terceros_cedula',
];

/**
* Aquí ocurre la magia. Le decimos a Laravel que cuando lea la columna 'estado'
* de la base de datos, la convierta automáticamente a nuestro Enum.
*/
protected $casts = [
'estado' => NotificacionEstadoEnum::class,
];

/**
* Esta función crea la relación con el modelo Credito.
* Significa que "Una Notificación pertenece a un Crédito".
* Te permitirá hacer: $notificacion->credito para obtener los datos del crédito.
*/
public function credito(): BelongsTo
{
return $this->belongsTo(Credito::class, 'cre_creditos_id');
}

/**
* Esta función crea la relación con el modelo Tercero (el cliente).
* Significa que "Una Notificación pertenece a un Tercero".
* Le decimos que se conecte usando la columna 'mae_terceros_cedula'.
*/
public function tercero(): BelongsTo
{
// El segundo argumento es la llave foránea en esta tabla ('cre_notificaciones').
// El tercer argumento es la columna correspondiente en la otra tabla ('mae_terceros').
return $this->belongsTo(maeTerceros::class, 'mae_terceros_cedula', 'cedula');
}
}