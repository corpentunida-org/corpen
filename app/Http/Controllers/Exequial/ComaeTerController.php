<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ComaeTerController extends Controller
{
    public function show($id)
    {
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Pastors', [
             'documentId' => $id,
        ]);
        $titular = $response->json();
        if (isset($titular['name'])) {
            return response()->json(['name' => $titular['name']]);
        } else {
            return response()->json(['error' => 'Name not found'], 404);
        }
    }
}
