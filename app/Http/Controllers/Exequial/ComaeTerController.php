<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;

class ComaeTerController extends Controller
{
    public function __construct(private ExequialApiService $api)
    {
    }

    public function show($id)
    {
        try {
            $response = $this->api->get('/api/Pastors', ['documentId' => $id]);
        } catch (ExequialApiException $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }

        $titular = $response->json();
        if (isset($titular['name'])) {
            return response()->json(['name' => $titular['name']]);
        } else {
            return response()->json(['error' => 'Name not found'], 404);
        }
    }
}
