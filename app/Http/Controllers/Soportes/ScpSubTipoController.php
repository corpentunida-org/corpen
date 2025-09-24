<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSubTipo; // <--- ¡MUY IMPORTANTE! ASEGÚRATE DE QUE ESTÉ AQUÍ.
// Asegúrate también de que ScpTipo no está duplicado o mal referenciado si lo usas.
use Illuminate\Http\Request;

class ScpSubTipoController extends Controller
{
    public function getByTipo($tipoId)
    {
        // 1. CONFIRMA QUE ESTO ES 'scp_tipo_id'
        $subTipos = ScpSubTipo::where('scp_tipo_id', $tipoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($subTipos);
    }
}