<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ParentescosController extends Controller
{
    public function index()
    {
        // $parentescos = Parentescos::all();
        // return response()->json($parentescos);

        $response = Http::get(env('API_PRODUCCION') . '/api/Exequiales/Relationship');
        $posts = $response->json();
        return response()->json($posts);
    }

    public function show($nomParentesco){
        $response = Http::get(env('API_PRODUCCION') . '/api/Exequiales/Relationship');
        $parentescos = $response->json();
        foreach ($parentescos as $parentesco) {
            if ($parentesco['name'] === $nomParentesco) {
                $codParentesco = $parentesco['code'];
                return $codParentesco;
            }
        }
    }

    public function showName($codParentesco){
        $response = Http::get(env('API_PRODUCCION') . '/api/Exequiales/Relationship');
        $parentescos = $response->json();
        foreach ($parentescos as $parentesco) {
            if ($parentesco['code'] === $codParentesco) {
                $nomParentesco = $parentesco['name'];
                return $nomParentesco;
            }
        }
    }
}
