<?php

namespace App\Models\Maestras;

use Illuminate\Database\Eloquent\Model;

/**
 * Reutiliza la tabla `parentescos` que ya existe (usada por Exequiales y Seguros) —
 * no se crea una tabla nueva. El controlador de Exequiales para "parentescos" consulta
 * una API externa (env('API_PRODUCCION')), pero la tabla local sigue siendo la fuente
 * real usada por SegBeneficiarioController/SegReclamacionesController/MaeC_ExSerController.
 */
class Parentesco extends Model
{
    protected $table = 'parentescos';

    public $timestamps = false;
}
