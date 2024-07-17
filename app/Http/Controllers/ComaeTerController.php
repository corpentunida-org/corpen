<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

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
