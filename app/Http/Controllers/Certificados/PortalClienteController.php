<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Importación del modelo principal del Front Desk
use App\Models\Maestras\MaeTerceros;
// (Opcional) Importar operaciones si se consultan lecturas directamente aquí
use App\Models\Certificados\CarSiaOperacion;

class PortalClienteController extends Controller
{
    /**
     * 1. PORTAL DE ATENCIÓN: Muestra la vista principal de búsqueda/login
     */
    public function index()
    {
        return view('sia.frontdesk.index');
    }

    /**
     * 2. AUTENTICA Y FILTRA POR NIT: Valida existencia y evalúa bloqueos
     */
    public function autenticarPorNit(Request $request)
    {
        $request->validate([
            'cod_ter' => 'required|string|max:50'
        ]);

        try {
            // Consulta de lectura del Tercero
            $tercero = MaeTerceros::where('cod_ter', $request->cod_ter)->first();

            // Validación de existencia
            if (!$tercero) {
                return redirect()->back()->with('error', 'El NIT/Cédula ingresado no se encuentra registrado en el sistema.');
            }

            // 3. EVALÚA PERMITIDOS: Verificación de estado financiero u operativo
            // Se asume que 'bloqueo' u otros campos booleanos/char determinan el acceso
            if ($tercero->bloqueo === 'S' || $tercero->bloqueo === 1 || $tercero->bloqueo === true) {
                Log::warning("SIA FrontDesk - Intento de acceso de tercero bloqueado: {$tercero->cod_ter}");
                return redirect()->back()->with('error', 'El usuario presenta un bloqueo activo. Por favor, comuníquese con cartera.');
            }

            // Almacenar en sesión (o token) que el tercero fue autenticado exitosamente en el portal
            session(['tercero_autenticado_cod' => $tercero->cod_ter]);

            return redirect()->route('sia.frontdesk.dashboard')
                             ->with('success', "Bienvenido(a) {$tercero->nom_ter} {$tercero->apl1}");

        } catch (\Exception $e) {
            Log::error("SIA FrontDesk - Error autenticando NIT {$request->cod_ter}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al intentar validar la información.');
        }
    }

    /**
     * 4. CONSULTA LECTURAS: Dashboard principal del cliente con sus operaciones
     */
    public function consultarLecturas()
    {
        // Verifica que exista una sesión activa desde la autenticación previa
        $cod_ter = session('tercero_autenticado_cod');

        if (!$cod_ter) {
            return redirect()->route('sia.frontdesk.index')->with('error', 'Su sesión ha expirado. Por favor ingrese su NIT nuevamente.');
        }

        try {
            // Carga el tercero con sus operaciones asociadas (si agregaste la relación 'operacionesSia')
            // o consulta las operaciones directamente si prefieres separar las consultas.
            $tercero = MaeTerceros::findOrFail($cod_ter);

            $operaciones = CarSiaOperacion::with(['estados.estado', 'lineas'])
                                          ->where('id_tercero', $cod_ter)
                                          ->orderBy('created_at', 'desc')
                                          ->get();

            return view('sia.frontdesk.dashboard', compact('tercero', 'operaciones'));

        } catch (\Exception $e) {
            Log::error("SIA FrontDesk - Error consultando lecturas para NIT {$cod_ter}: " . $e->getMessage());
            return redirect()->route('sia.frontdesk.index')
                             ->with('error', 'No fue posible cargar las operaciones. Intente más tarde.');
        }
    }

    /**
     * Cierra la sesión del portal de atención
     */
    public function logout()
    {
        session()->forget('tercero_autenticado_cod');
        return redirect()->route('sia.frontdesk.index')->with('success', 'Sesión cerrada correctamente.');
    }
}
