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

        $response = Http::get('https://www.siasoftapp.com:7011/api/Exequiales/Relationship');
        $posts = $response->json();
        return response()->json($posts);
    }

    public function show($nomParentesco){
        $response = Http::get('https://www.siasoftapp.com:7011/api/Exequiales/Relationship');
        $parentescos = $response->json();
        foreach ($parentescos as $parentesco) {
            if ($parentesco['name'] === $nomParentesco) {
                $codParentesco = $parentesco['code'];
                return $codParentesco;
            }
        }
    }

    public function showName($codParentesco){
        $response = Http::get('https://www.siasoftapp.com:7011/api/Exequiales/Relationship');
        $parentescos = $response->json();
        foreach ($parentescos as $parentesco) {
            if ($parentesco['code'] === $codParentesco) {
                $nomParentesco = $parentesco['name'];
                return $nomParentesco;
            }
        }
    }
}