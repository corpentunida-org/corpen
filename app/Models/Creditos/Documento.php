<?php

namespace App\Models\Creditos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
use HasFactory;

/**
* La tabla de la base de datos asociada con el modelo.
*
* @var string
*/
protected $table = 'cre_documentos';

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
'ruta_archivo',
'fecha_subida',
'observaciones',
'cre_creditos_id',
'cre_tipo_documentos_id',
'id_unico_documento', // Relacionado con pagarés, escrituras, etc.
];

/**
* Los atributos que deben ser convertidos a tipos nativos.
*
* @var array<string, string>
*/
protected $casts = [
'fecha_subida' => 'date',
];

/**
* Obtiene el crédito al que pertenece este documento.
*/
public function credito(): BelongsTo
{
return $this->belongsTo(Credito::class, 'cre_creditos_id');
}

/**
* Obtiene el tipo de documento.
*/
public function tipoDocumento(): BelongsTo
{
return $this->belongsTo(TipoDocumento::class, 'cre_tipo_documentos_id');
}

/*
* Nota sobre 'id_unico_documento':
* Este campo parece vincular este registro con un pagaré o escritura específico
* a través de su campo 'id_unico_documento'.
* Puedes crear una función para obtener el documento relacionado (pagaré/escritura)
* si lo necesitas, por ejemplo:
*
* public function getDocumentoFuenteAttribute()
* {
* $pagare = Pagare::where('id_unico_documento', $this->id_unico_documento)->first();
* if ($pagare) {
* return $pagare;
* }
* return Escritura::where('id_unico_documento', $this->id_unico_documento)->first();
* }
*/
}
