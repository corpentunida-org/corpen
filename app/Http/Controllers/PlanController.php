<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlanController extends Controller
{
    public function index()
    {
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Plan');
        $plans = $response->json();
        return response()->json($plans);
    }

    public function nomCodPlan($cod){        
        switch ($cod) {
            case '01':
                return 'Plan Basico';
                break;
            case '02':
                return 'Plan Ejecutivo';
                break;
            case '03':
                return 'Plan Unipersonal';
                break;
            case '04':
                return 'Plan Exento Pago';
                break;
            default:
                return ' ';
                break;
        }
    }
}
