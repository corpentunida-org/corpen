<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComaeTerController extends Controller
{
    public function show(Request $request, $id)
    {    
        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://www.siasoftapp.com:7011/api/Pastors', [
             'documentId' => $id,
        ]);
        
    }
    
}
