<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMantenimiento;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMovimiento;
use App\Models\Inventario\InvMovimientoDetalle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    public function index()
    {
        // Traemos los mantenimientos más recientes primero
        $mantenimientos = InvMantenimiento::with(['activo', 'creador'])->latest()->paginate(10);
        return view('inventario.mantenimientos.index', compact('mantenimientos'));
    }

public function create()
    {
        // Solo activos que NO estén dados de baja
        $activos = InvActivo::where('id_Estado', '!=', 3)->get(); 
        
        // Traemos a todos los usuarios para llenar los selects en la vista
        $usuarios = User::all(); 

        return view('inventario.mantenimientos.create', compact('activos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_InvActivos' => 'required|exists:inv_activos,id',
            'id_usersRegistro' => 'required|exists:users,id', // <-- Validación del usuario que registra
            'id_usersAsignado' => 'required|exists:users,id', // <-- Validación a quién se le asigna
            'costo_mantenimiento' => 'required|numeric|min:0',
            'detalle' => 'required|string',
            'acta_archivo' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120' 
        ]);

        $path = null; 

        try {
            DB::beginTransaction();

            $activo = InvActivo::findOrFail($request->id_InvActivos);

            if ($request->hasFile('acta_archivo')) {
                $archivo = $request->file('acta_archivo');
                $directory = "corpentunida/inventario/mantenimientos"; 
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $path = Storage::disk('s3')->putFileAs($directory, $archivo, $nombreArchivo);
            }

            // 3. Guardar registro histórico (Usamos el request en lugar de auth()->id())
            $mantenimiento = InvMantenimiento::create([
                'id_InvActivos' => $request->id_InvActivos,
                'costo_mantenimiento' => $request->costo_mantenimiento,
                'detalle' => $request->detalle,
                'acta' => $path, 
                'id_usersRegistro' => $request->id_usersRegistro // <-- DATO MANUAL
            ]);

            // 4. Crear el Movimiento principal
            $movimiento = InvMovimiento::create([
                'codigo_acta' => 'MANT-' . date('Ymd') . '-' . $mantenimiento->id, 
                'acta_archivo' => null, 
                'observacion_general' => 'Salida a mantenimiento: ' . $request->detalle,
                'id_InvTiposRegistros' => 12, // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
                'id_usersAsignado' => $request->id_usersAsignado, // <-- DATO MANUAL
                'id_usersRegistro' => $request->id_usersRegistro, // <-- DATO MANUAL
                'id_mantenimiento' => $mantenimiento->id 
            ]);

            // 5. Crear el Detalle del Movimiento
            InvMovimientoDetalle::create([
                'estado_individual' => 'Enviado a revisión', 
                'id_InvMovimientos' => $movimiento->id, 
                'id_InvActivos' => $activo->id,
                'id_estado' => 12, // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
                'id_usersDelActivo' => $request->id_usersAsignado // <-- DATO MANUAL
            ]);

            // 6. Actualizar estado del activo
            $activo->id_Estado = 12; // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
            $activo->save();

            DB::commit();
            return redirect()->route('inventario.mantenimientos.index')->with('success', 'Mantenimiento registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($path) { Storage::disk('s3')->delete($path); }

            return back()->withInput()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }
    
    /**
     * Sube el soporte/factura a AWS S3 desde la tabla (Index)
     */
    public function uploadActa(Request $request, $id)
    {
        $request->validate([
            'acta_archivo' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('acta_archivo')) {
            $mantenimiento = InvMantenimiento::findOrFail($id);

            $archivo = $request->file('acta_archivo');
            $directory = "corpentunida/inventario/mantenimientos"; 
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            $path = Storage::disk('s3')->putFileAs($directory, $archivo, $nombreArchivo);

            $mantenimiento->acta = $path;
            $mantenimiento->save();

            return back()->with('success', '¡Factura o acta subida y vinculada correctamente!');
        }

        return back()->with('error', 'No se detectó ningún archivo para subir.');
    }
}