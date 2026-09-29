<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Maestras\MaeTerceros;
use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaTipoOperacion;

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

    public function consultarLecturas(Request $request)
    {
        $cod_ter = session('tercero_autenticado_cod');

        if (!$cod_ter) {
            return redirect()->route('certificados.frontdesk.index')->with('error', 'Su sesión ha expirado. Por favor ingrese su NIT nuevamente.');
        }

        try {
            // 1. Buscar al tercero para mostrar sus datos en la vista
            $tercero = MaeTerceros::where('cod_ter', $cod_ter)->firstOrFail();

            // 2. Capturar filtros opcionales de Mes y Año desde la interfaz del cliente
            $mesSeleccionado = $request->input('mes');
            $anioSeleccionado = $request->input('anio', now()->year);

            // 3. Consultar las operaciones base del tercero
            $query = CarSiaOperacion::with(['estados.estado', 'lineas.lineaSia'])
                ->where('id_tercero', $cod_ter);

            // Filtrar por año si es provisto
            if ($anioSeleccionado) {
                $query->whereYear('created_at', $anioSeleccionado);
            }

            // Filtrar por mes si es provisto
            if ($mesSeleccionado) {
                $query->whereMonth('created_at', $mesSeleccionado);
            }

            $operaciones = $query->orderBy('created_at', 'desc')->get();

            // 4. Enriquecer cada operación con sus tipos de certificados dinámicos e historial (Lógica adaptada de show)
            $operaciones->each(function ($operacion) {
                $primeraLinea = $operacion->lineas->sortByDesc('created_at')->first();
                $ultimoHash = $primeraLinea?->hash_certificado ?? null;

                if ($ultimoHash) {
                    $tipoAsociado = CarSiaTipoOperacion::with('tipo')
                        ->where('numero_bloque', $operacion->numero_bloque)
                        ->where(function($q) use ($operacion) {
                            $q->where('id_car_sia_operaciones', $operacion->id)
                                ->orWhereNull('id_car_sia_operaciones');
                        })
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $operacion->ultimo_hash = $ultimoHash;
                    $operacion->ultimo_tipo = optional($tipoAsociado)->tipo;
                } else {
                    $operacion->ultimo_hash = null;
                    $operacion->ultimo_tipo = null;
                }

                // Cargar dinámicamente los tipos de certificados disponibles para esta operación (evitando duplicados)
                $operacion->historialTiposDisponibles = CarSiaTipoOperacion::with('tipo')
                    ->where('id_car_sia_operaciones', $operacion->id)
                    ->orWhere(function($q) use ($operacion) {
                        $q->where('numero_bloque', $operacion->numero_bloque)
                        ->whereNull('id_car_sia_operaciones');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->unique('id_car_sia_tipos')
                    ->map(function ($registro) use ($operacion) {
                        $lineasParaEsteTipo = collect($operacion->lineas)->filter(function($linea) use ($registro) {
                            if ($linea->id_car_sia_tipos == $registro->id_car_sia_tipos) return true;
                            if (empty($linea->id_car_sia_tipos) && !str_contains($linea->hash_certificado, '-TIPO-')) return true;
                            if (str_contains($linea->hash_certificado, "-TIPO-{$registro->id_car_sia_tipos}-")) return true;
                            return false;
                        });

                        $versionesDeEsteTipo = collect($lineasParaEsteTipo)->groupBy('hash_certificado')->map(function($grupo) {
                            return collect($grupo)->first();
                        })->sortByDesc('created_at');

                        $registro->hashActual = $versionesDeEsteTipo->first()->hash_certificado ?? null;
                        return $registro;
                    });
            });

            // 5. Obtener los años disponibles para poblar el selector de filtros en la vista
            $aniosDisponibles = CarSiaOperacion::where('id_tercero', $cod_ter)
                ->whereNotNull('created_at')
                ->selectRaw('YEAR(created_at) as anio')
                ->groupBy('anio')
                ->orderBy('anio', 'desc')
                ->pluck('anio');

            if ($aniosDisponibles->isEmpty()) {
                $aniosDisponibles = collect([now()->year]);
            }

            return view('certificados.frontdesk.dashboard', compact(
                'tercero',
                'operaciones',
                'aniosDisponibles',
                'mesSeleccionado',
                'anioSeleccionado'
            ));

        } catch (\Exception $e) {
            Log::error("CERTIFICADOS FrontDesk - Error consultando lecturas para NIT {$cod_ter}: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());

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
