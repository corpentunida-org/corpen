<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Imports\ExcelExport;
use App\Models\Maestras\TerceroImportacion;
use App\Services\Maestras\TerceroImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TerceroImportController extends Controller
{
    public function __construct(private TerceroImportService $servicio)
    {
    }

    public function index()
    {
        $importaciones = TerceroImportacion::with('user')->latest()->paginate(10);

        return view('maestras.terceros.importar.index', compact('importaciones'));
    }

    public function analizar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $nombreOriginal = $request->file('archivo')->getClientOriginalName();
        $token = Str::uuid()->toString();
        $ruta = $request->file('archivo')->storeAs('importaciones-terceros', $token . '.xlsx');

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $analisis = $this->servicio->analizar($filas);

        return view('maestras.terceros.importar.preview', [
            'analisis' => $analisis,
            'token' => $token,
            'archivoNombre' => $nombreOriginal,
        ]);
    }

    public function confirmar(Request $request)
    {
        $this->ampliarMemoria();

        $request->validate(['token' => ['required', 'string'], 'archivo_nombre' => ['required', 'string']]);

        $ruta = 'importaciones-terceros/' . $request->input('token') . '.xlsx';

        if (!Storage::exists($ruta)) {
            return redirect()->route('maestras.terceros.importar.index')
                ->with('error', 'El archivo ya expiró o fue procesado. Vuelve a subirlo.');
        }

        $filas = $this->servicio->leerExcel(Storage::path($ruta));
        $resultado = $this->servicio->aplicar($filas);

        TerceroImportacion::create([
            'user_id' => Auth::id(),
            'archivo_nombre' => $request->input('archivo_nombre'),
            'resumen' => $resultado,
        ]);

        Storage::delete($ruta);

        return redirect()->route('maestras.terceros.importar.index')->with(
            'success',
            "Importación aplicada: {$resultado['insertados']} pastores nuevos creados. De los {$resultado['ya_existian']} que ya existían, {$resultado['enriquecidos']} recibieron datos nuevos en campos que tenían vacíos. {$resultado['excepciones']} filas quedaron fuera por excepciones."
        );
    }

    /**
     * Plantilla en blanco con los encabezados que reconoce el importador (ver
     * TerceroImportService::ENCABEZADOS_ACEPTADOS) y una fila de ejemplo mostrando el formato
     * esperado — para no tener que adivinar la estructura de memoria cada mes.
     */
    public function plantilla()
    {
        $ejemplo = [
            '1234567890', 'JUAN PEREZ GOMEZ', 'BOGOTA (CUNDINAMARCA)', 'G', 'O+',
            '6011234567', '3101234567', 'juan.perez@correo.com', '1 - NOMBRE DE LA CONGREGACION',
            '15/03/1980', '9876543210', 'MARIA LOPEZ RAMIREZ',
        ];

        return Excel::download(
            new ExcelExport([$ejemplo], TerceroImportService::HEADER_PLANTILLA),
            'Plantilla_Importar_Pastores_IPUC.xlsx'
        );
    }

    /**
     * Cargar este Excel completo (miles de filas) ya usa ~250MB solo en PhpSpreadsheet en una
     * sola pasada — medido directamente, no una suposición. Con el resto de Laravel encima eso
     * deja poco margen frente al memory_limit por defecto (500M). Se sube solo para estas dos
     * acciones (poco frecuentes, con permiso propio), no de forma global.
     */
    private function ampliarMemoria(): void
    {
        ini_set('memory_limit', '1024M');
    }
}
