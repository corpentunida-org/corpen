<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlanController extends Controller
{
    public function index()
    {
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Plan');
        $plans = $response->json();
        return response()->json($plans);
    }

    public function nomCodPlan($cod){        
        switch ($cod) {
            case '01':
                return 'Plan Basico';
            case '02':
                return 'Plan Ejecutivo';
            case '03':
                return 'Plan Unipersonal';
            case '04':
                return 'Plan Exento Pago';
            default:
                return ' ';
        }
    }
}
