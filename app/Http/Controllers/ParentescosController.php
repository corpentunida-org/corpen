<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parentescos;
use Illuminate\Support\Facades\Http;

class ParentescosController extends Controller
{
    public function index()
    {
        // $parentescos = Parentescos::all();
        // return response()->json($parentescos);
        //$response = Http::get('https://www.siasoftapp.com:7011/api/Exequiales/Relationship');
        $token = env('TOKEN_ADMIN');
        $id = '7514540';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Exequiales', [
            'documentId' => $id,
        ]);
        $posts = $response->json();
        return response()->json($posts);
    }
}