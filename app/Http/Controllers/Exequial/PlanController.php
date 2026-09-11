<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Models\Exequiales\Plan;

class PlanController extends Controller
{
    /**
     * Catálogo local (tabla `planes`) en vez de SiaSoft — antes esto dependía de la API externa
     * y los nombres de nomCodPlan() estaban hardcodeados por separado en un switch.
     */
    public function index()
    {
        return Plan::orderBy('code')->get(['code', 'name'])->toArray();
    }

    public function nomCodPlan($cod)
    {
        $code = str_pad((string) $cod, 2, '0', STR_PAD_LEFT);
        return Plan::find($code)?->name ?? ' ';
    }
}
