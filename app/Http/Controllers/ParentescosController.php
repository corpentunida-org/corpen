<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parentescos;

class ParentescosController extends Controller
{
    public function index()
    {
        $parentescos = Parentescos::all();
        //return response()->json("hola");
        return  "hellow";
    }
}
