<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documentos';

    protected $fillable = ['codigo', 'nombre'];
}
