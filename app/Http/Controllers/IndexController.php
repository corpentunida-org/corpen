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
        $userEmail = Auth::user()->email;
        $roles = \DB::table('roles')
                ->join('actions', 'roles.id', '=', 'actions.role_id')
                ->where('actions.user_email', '=', $userEmail)
                ->select('roles.*')
                ->get();

        if ($roles->first()->name===('admin')) {
            return redirect()->route('admin.users.index');
        } elseif ($roles->first()->name===('exequial')) {
            return view('exequial.asociados.index');
        } elseif ($roles->first()->name===('creditos')) {
            return view('exequial.asociados.index');
        } elseif ($roles->first()->name===('seguros')) {
            return redirect()->route('seguros.poliza.index');
        } elseif ($roles->first()->name===('cinco')) {
            return redirect()->route('cinco.tercero.index');
        } elseif ($roles->first()->name===('cartera')) {
            return redirect()->route('cartera.morosos.index');
        } else {
                return view('welcome');
            }
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
