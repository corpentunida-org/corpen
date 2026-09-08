<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;

class PlanController extends Controller
{
    public function __construct(private ExequialApiService $api)
    {
    }

    public function index()
    {
        try {
            $response = $this->api->get('/api/Plan');
            return $response->successful() ? $response->json() : [];
        } catch (ExequialApiException $e) {
            return [];
        }
    }

    public function nomCodPlan($cod)
    {
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
