<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Maestras\MaeTerceros;
use App\Models\Certificados\CarSiaOperacion;

class PortalClienteController extends Controller
{
    public function index()
    {
        return view('certificados.frontdesk.index');
    }

    public function autenticarPorNit(Request $request)
    {
        $request->validate([
            'cod_ter' => 'required|string|max:50'
        ]);

        try {
            $tercero = MaeTerceros::where('cod_ter', $request->cod_ter)->first();

            if (!$tercero) {
                return redirect()->back()->with('error', 'El NIT/Cédula ingresado no se encuentra registrado en el sistema.');
            }

            if ($tercero->bloqueo === 'S' || $tercero->bloqueo === 1 || $tercero->bloqueo === true) {
                Log::warning("SIA FrontDesk - Intento de acceso de tercero bloqueado: {$tercero->cod_ter}");
                return redirect()->back()->with('error', 'El usuario presenta un bloqueo activo. Por favor, comuníquese con cartera.');
            }

            session(['tercero_autenticado_cod' => $tercero->cod_ter]);

            return redirect()->route('certificados.frontdesk.dashboard')
                             ->with('success', "Bienvenido(a) {$tercero->nom_ter} " . ($tercero->apl1 ?? ''));

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS FrontDesk - Error autenticando NIT {$request->cod_ter}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al intentar validar la información.');
        }
    }

    public function consultarLecturas()
    {
        $cod_ter = session('tercero_autenticado_cod');

        if (!$cod_ter) {
            return redirect()->route('certificados.frontdesk.index')->with('error', 'Su sesión ha expirado. Por favor ingrese su NIT nuevamente.');
        }

        try {
            // Buscamos al tercero por su NIT para mostrar sus datos en la vista
            $tercero = MaeTerceros::where('cod_ter', $cod_ter)->firstOrFail();

            // CORRECCIÓN: Volvemos a buscar las operaciones usando el NIT ($cod_ter) 
            // que es como lo tenías originalmente y funcionaba.
            $operaciones = CarSiaOperacion::with(['estados.estado', 'lineas'])
                                          ->where('id_tercero', $cod_ter) 
                                          ->orderBy('created_at', 'desc')
                                          ->get();

            return view('certificados.frontdesk.dashboard', compact('tercero', 'operaciones'));

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS FrontDesk - Error consultando lecturas para NIT {$cod_ter}: " . $e->getMessage());
            return redirect()->route('certificados.frontdesk.index')
                             ->with('error', 'No fue posible cargar las operaciones. Intente más tarde.');
        }
    }

    public function logout()
    {
        session()->forget('tercero_autenticado_cod');
        return redirect()->route('certificados.frontdesk.index')->with('success', 'Sesión cerrada correctamente.');
    }
}