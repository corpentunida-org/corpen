<?php

namespace App\Http\Controllers\Integraciones;

use App\Http\Controllers\Controller;
use App\Models\Integraciones\LogIntegracion;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PanelIntegracionController extends Controller
{
    /**
     * Muestra el panel principal con el estado actual y el historial de logs (index.blade.php)
     */
    public function index()
    {
        // Traemos los últimos 10 intentos de conexión
        $logs = LogIntegracion::latest()->take(10)->get();

        // Verificación rápida del estado actual de la API
        $urlPastors = env('API_PRODUCCION') . "/api/Pastors";
        $estadoPastors = 'Desconocido';

        try {
            $response = Http::withToken(env('TOKEN_ADMIN'))->get($urlPastors);
            $estadoPastors = $response->successful() ? 'Conectado' : 'Fallo / No Autorizado';
        } catch (\Exception $e) {
            $estadoPastors = 'Inaccesible (Servidor Caído)';
        }

        return view('integraciones.index', compact('estadoPastors', 'logs'));
    }

    /**
     * Muestra la vista de detalle y simulador para una API específica (show.blade.php)
     */
    public function show()
    {
        return view('integraciones.show');
    }

    /**
     * Ejecuta una prueba de conexión, recibe datos dinámicos, mide el tiempo y guarda el log.
     */
    public function testPastors(Request $request)
    {
        // 1. Recibimos el documento del formulario (si viene vacío, usamos uno por defecto de prueba)
        $documento = $request->input('documento', '1077091759');

        $url = env('API_PRODUCCION') . "/api/Pastors";
        $token = env('TOKEN_ADMIN');

        // Iniciamos el cronómetro
        $inicio = microtime(true);

        try {
            // 2. Pasamos el documento dinámico a la petición
            $response = Http::withToken($token)->get($url, ['DocumentId' => $documento]);

            // Calculamos cuánto tiempo tardó
            $tiempoMs = round((microtime(true) - $inicio) * 1000);

            // Guardamos el registro en la BD
            LogIntegracion::create([
                'nombre_api'          => 'API Pastors',
                'endpoint'            => $url,
                'metodo'              => 'GET',
                'codigo_respuesta'    => $response->status(),
                'tiempo_respuesta_ms' => $tiempoMs,
                'estado'              => $response->successful() ? 'Exitoso' : 'Error',
                'mensaje_error'       => $response->successful() ? null : $response->body(),
            ]);

            // 3. Manejamos la respuesta
            if ($response->successful()) {
                // Formateamos el JSON para que se vea bonito en la vista "show"
                $jsonBonito = json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                return back()->with('success', "¡Conexión exitosa! Tiempo: {$tiempoMs}ms. Log guardado.")
                             ->with('resultado_json', $jsonBonito); // Enviamos el JSON a la vista
            } else {
                return back()->with('error', "La API respondió con el código {$response->status()}. Revisa la tabla de logs.");
            }

        } catch (\Exception $e) {
            $tiempoMs = round((microtime(true) - $inicio) * 1000);

            LogIntegracion::create([
                'nombre_api'          => 'API Pastors',
                'endpoint'            => $url,
                'metodo'              => 'GET',
                'codigo_respuesta'    => null,
                'tiempo_respuesta_ms' => $tiempoMs,
                'estado'              => 'Error Crítico',
                'mensaje_error'       => $e->getMessage(),
            ]);

            return back()->with('error', 'El servidor no responde: ' . $e->getMessage());
        }
    }
}
