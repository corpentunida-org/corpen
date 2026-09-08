<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = \DB::table('roles')
                ->join('actions', 'roles.id', '=', 'actions.role_id')
                ->where('actions.user_id', '=', Auth::id())
                ->select('roles.*')
                ->get();
        
        if ($roles->first()->name===('admin')) {
            return redirect()->route('admin.users.index');
        } elseif ($roles->first()->name===('exequial')) {
            return redirect()->route('exequial.asociados.index');
        } elseif ($roles->first()->name===('seguros')) {
            return redirect()->route('seguros.poliza.index');
        } elseif ($roles->first()->name===('cinco')) {
            return redirect()->route('cinco.tercero.index');
        } elseif ($roles->first()->name===('soporte')) {
            return redirect()->route('soportes.soportes.index');
        } else {
                return view('welcome');
            }
        }

    /**
     * Pantalla offline del PWA (vendor/laravelpwa) — antes vivía como closure en routes/web.php,
     * lo que impedía usar `route:cache` (Laravel no puede serializar closures).
     */
    public function offline()
    {
        return view('vendor.laravelpwa.offline');
    }

    /**
     * Layout base sin contenido — antes era un closure en routes/web.php idéntico a este
     * (mismo bug). layouts/base.blade.php es en realidad la plantilla del componente
     * <x-base-layout>, que recibe su contenido vía $slot; renderizada directo con view() sin
     * pasar por el componente, $slot nunca se define y PHP la trata como error fatal bajo el
     * kernel HTTP real (aunque en consola solo se veía como warning, por eso pasó desapercibido).
     */
    public function base()
    {
        return view('layouts.base', ['slot' => '']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
