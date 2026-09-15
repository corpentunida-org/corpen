<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Imports\ExcelExport;
use App\Models\Maestras\ComaeTerImportacion;
use App\Services\Maestras\ComaeTerImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ComaeTerImportController extends Controller
{
    public function __construct(private ComaeTerImportService $servicio)
    {
    }

    public function index()
    {
        $importaciones = ComaeTerImportacion::with('user')->latest()->paginate(10);

        return view('maestras.comaeter.importar.index', compact('importaciones'));
    }

    public function analizar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $nombreOriginal = $request->file('archivo')->getClientOriginalName();
        $token = Str::uuid()->toString();
        $ruta = $request->file('archivo')->storeAs('importaciones-comae-ter', $token . '.xls');

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $analisis = $this->servicio->analizar($filas);

        return view('maestras.comaeter.importar.preview', [
            'analisis' => $analisis,
            'token' => $token,
            'archivoNombre' => $nombreOriginal,
        ]);
    }

    public function confirmar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate(['token' => ['required', 'string'], 'archivo_nombre' => ['required', 'string']]);

        $ruta = 'importaciones-comae-ter/' . $request->input('token') . '.xls';

        if (!Storage::exists($ruta)) {
            return redirect()->route('maestras.comaeter.importar.index')
                ->with('error', 'El archivo ya expiró o fue procesado. Vuelve a subirlo.');
        }

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $resultado = $this->servicio->aplicar($filas);

        ComaeTerImportacion::create([
            'user_id' => Auth::id(),
            'archivo_nombre' => $request->input('archivo_nombre'),
            'resumen' => $resultado,
        ]);

        Storage::delete($ruta);

        return redirect()->route('maestras.comaeter.importar.index')->with(
            'success',
            "Importación aplicada: {$resultado['insertados']} terceros nuevos creados. De los {$resultado['ya_existian']} que ya existían, {$resultado['enriquecidos']} recibieron datos nuevos en campos que tenían vacíos."
        );
    }

    /**
     * Plantilla en blanco con las ~157 columnas reales de MaeTerceros que este import reconoce
     * (ver ComaeTerImportService::columnasPlantilla) más una fila de ejemplo mostrando solo la
     * identificación — el resto de columnas se deja vacío a propósito: en la práctica el archivo
     * real es un export directo del sistema externo con estos mismos encabezados, así que la
     * plantilla es sobre todo para dejar constancia de qué columnas se reconocen.
     */
    public function plantilla()
    {
        $columnas = $this->servicio->columnasPlantilla();
        $ejemplo = array_fill_keys($columnas, '');
        $ejemplo['cod_ter'] = '1234567890';
        $ejemplo['nom_ter'] = 'JUAN PEREZ GOMEZ';

        return Excel::download(
            new ExcelExport([array_values($ejemplo)], $columnas),
            'Plantilla_Actualizar_Terceros_CoMae_ter.xlsx'
        );
    }

    /**
     * Este archivo real mide ~22.170 filas x 162 columnas (~29MB) — medido directamente:
     * cargarlo con setReadDataOnly ya usa ~1GB de pico solo en PhpSpreadsheet, antes de sumar el
     * array de filas y las consultas de comparación contra MaeTerceros. leerExcel + analizar +
     * aplicar completo mide ~70s de punta a punta contra la base real. Se sube solo para estas
     * dos acciones (poco frecuentes, con permiso propio), no de forma global.
     */
    private function ampliarMemoria(): void
    {
        ini_set('memory_limit', '3072M');
        ini_set('max_execution_time', '300');
    }
}
