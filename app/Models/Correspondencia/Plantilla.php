<?php

namespace App\Models\Correspondencia;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plantilla extends Model
{
    use HasFactory;

    protected $table = 'corr_plantillas';

    protected $fillable = [
        'nombre_plantilla',
        'html_base',
    ];
}
