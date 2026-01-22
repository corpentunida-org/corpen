<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMovimiento;
use App\Models\Inventario\InvMovimientoDetalle;
use App\Models\Inventario\InvActivo;
use App\Models\User;
use App\Models\Inventario\InvEstado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index()
    {
        $movimientos = InvMovimiento::with(['responsable', 'tipoRegistro'])->paginate(10);
        return view('inventario.movimientos.index', compact('movimientos'));
    }

    public function create()
    {
        $usuarios = User::all();
        // Cargamos tipos de movimiento (Asignación, Devolución, Préstamo)
        // Usamos InvEstado como dijimos que actuaría como Tipo de Registro
        $tipos = InvEstado::all(); 
        
        // Solo mostrar activos disponibles para asignar
        $activosDisponibles = InvActivo::where('id_Estado', 1)->get(); 

        return view('inventario.movimientos.create', compact('usuarios', 'tipos', 'activosDisponibles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_acta' => 'required|unique:inv_movimientos',
            'id_usersAsignado' => 'required',
            'activos_seleccionados' => 'required|array' // IDs de activos checkbox
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Cabecera del Acta
            $movimiento = InvMovimiento::create([
                'codigo_acta' => $request->codigo_acta,
                'observacion_general' => $request->observacion_general,
                'id_InvTiposRegistros' => $request->id_InvTiposRegistros, // Asignación o Devolución
                'id_usersAsignado' => $request->id_usersAsignado,
                'id_usersRegistro' => auth()->id()
            ]);

            // 2. Procesar cada activo
            foreach ($request->activos_seleccionados as $activo_id) {
                
                // Guardar en detalle del acta
                InvMovimientoDetalle::create([
                    'id_InvMovimientos' => $movimiento->id,
                    'id_InvActivos' => $activo_id,
                    'id_InvTiposRegistros' => $request->id_InvTiposRegistros,
                    'id_usersDelActivo' => $request->id_usersAsignado
                ]);

                // 3. ACTUALIZAR EL ACTIVO (Lógica vital)
                $activo = InvActivo::find($activo_id);
                
                // Si es Asignación (id 2, por ejemplo)
                if ($request->id_InvTiposRegistros == 2) { 
                    $activo->id_Estado = 2; // Estado "Asignado"
                    $activo->id_ultimo_usuario_asignado = $request->id_usersAsignado;
                } 
                // Si es Devolución (id 1, por ejemplo)
                elseif ($request->id_InvTiposRegistros == 1) {
                    $activo->id_Estado = 1; // Vuelve a "Disponible"
                    $activo->id_ultimo_usuario_asignado = null;
                }
                
                $activo->save();
            }

            DB::commit();
            return redirect()->route('movimientos.index')->with('success', 'Acta creada y activos actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}