<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\CongregacionImportacion;
use App\Services\Maestras\CongregacionImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CongregacionImportController extends Controller
{
    public function __construct(private CongregacionImportService $servicio)
    {
    }

    public function index()
    {
        $importaciones = CongregacionImportacion::with('user')->latest()->paginate(10);

        return view('maestras.congregaciones.importar.index', compact('importaciones'));
    }

    /**
     * Sube el Excel y muestra el análisis (qué se insertaría/actualizaría y las excepciones)
     * sin escribir nada en la BD todavía.
     */
    public function analizar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $nombreOriginal = $request->file('archivo')->getClientOriginalName();
        $token = Str::uuid()->toString();
        $ruta = $request->file('archivo')->storeAs('importaciones-congregaciones', $token . '.xlsx');

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $analisis = $this->servicio->analizar($filas);

        return view('maestras.congregaciones.importar.preview', [
            'analisis' => $analisis,
            'token' => $token,
            'archivoNombre' => $nombreOriginal,
        ]);
    }

    /**
     * Re-lee el mismo archivo y aplica el plan, re-validando contra la BD en el momento (no el
     * análisis que se mostró antes, por si algo cambió mientras tanto).
     */
    public function confirmar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate(['token' => ['required', 'string'], 'archivo_nombre' => ['required', 'string']]);

        $ruta = 'importaciones-congregaciones/' . $request->input('token') . '.xlsx';

        if (!Storage::exists($ruta)) {
            return redirect()->route('maestras.congregacion.importar.index')
                ->with('error', 'El archivo ya expiró o fue procesado. Vuelve a subirlo.');
        }

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $resultado = $this->servicio->aplicar($filas);

        CongregacionImportacion::create([
            'user_id' => Auth::id(),
            'archivo_nombre' => $request->input('archivo_nombre'),
            'resumen' => $resultado,
        ]);

        Storage::delete($ruta);

        return redirect()->route('maestras.congregacion.importar.index')->with(
            'success',
            "Importación aplicada: {$resultado['insertados']} congregaciones nuevas, {$resultado['actualizados']} actualizadas, {$resultado['pastores_enlazados']} pastores enlazados en Maestra de Terceros. {$resultado['excepciones']} filas quedaron fuera por excepciones."
        );
    }

    /**
     * Cargar un Excel de miles de filas con PhpSpreadsheet ya usa ~250MB en una sola pasada
     * (medido directamente en el importador de Terceros, mismo tamaño de archivo). Con el resto
     * de Laravel encima eso deja poco margen frente al memory_limit por defecto (500M). Se sube
     * solo para estas dos acciones, no de forma global.
     */
    private function ampliarMemoria(): void
    {
        ini_set('memory_limit', '1024M');
    }
}
