<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use App\Models\Reserva\Res_inmueble;
use App\Models\Reserva\Res_inmueble_foto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservaInmueble;

class ResInmuebleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inmuebles = Res_inmueble::where('active', 1)
            ->with('fotosrel')->get()
            ->map(function ($inmueble) {
                $inmueble->fotosrel->map(function ($foto) {
                    if (Storage::disk('s3')->exists($foto->attached)) {
                        $foto->url = Storage::disk('s3')->temporaryUrl($foto->attached, now()->addMinutes(5));
                    }
                    return $foto;
                });
                return $inmueble;
            });        
        return view('reserva.inmuebles.index', compact('inmuebles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reserva.inmuebles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'required|string|max:500',
            'direccion' => 'required|string|max:200',
            'ciudad' => 'required|string|max:100',
            'maps' => 'required|url',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $inmueblecreado = Res_inmueble::create([
            'name' => $request->nombre,
            'description' => $request->descripcion,
            'address' => $request->direccion,
            'city' => $request->ciudad,
            'ubicacion' => $request->maps,
        ]);

        if ($request->hasFile('imagenes')) {
            $basePath = 'corpentunida/reserva/inmueble_' . $inmueblecreado->id;
            foreach ($request->file('imagenes') as $foto) {
                $path = Storage::disk('s3')->put($basePath, $foto);

                Res_inmueble_foto::create([
                    'res_inmueble_id' => $inmueblecreado->id,
                    'attached' => $path,
                ]);
            }
        }
        return redirect()->route('reserva.crudinmuebles.index')->with('success', 'Se agregó correctamente el nuevo inmueble');
    }

    /**
     * Display the specified resource.
     */
    public function show(Res_inmueble $inmueble)
    {
        $inmueble->load('fotosrel');
        $inmueble->fotosrel->transform(function ($foto) {
            $foto->ruta = Storage::disk('s3')->temporaryUrl($foto->attached, now()->addMinutes(5));
            return $foto;
        });

        return view('reserva.index', compact('inmueble'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update() {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
