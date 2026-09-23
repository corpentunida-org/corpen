<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntSeguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Admin → Limpiar Historial de Adjuntos: los soportes (evidencias) de Interacciones se suben a
 * S3 y hoy nunca se borran solos — con miles de archivos nuevos cada mes, esto le da a un
 * administrador una forma manual y controlada de liberar espacio borrando los adjuntos de años
 * ya cerrados, sin perder el historial de gestión (el seguimiento en sí se conserva siempre,
 * solo se quita el archivo pesado).
 *
 * Identifica qué borrar por la fecha guardada en la base de datos (int_seguimiento.created_at),
 * no por la carpeta de S3 — así funciona igual para los archivos viejos (carpetas sin año,
 * herencia de antes de este cambio) y los nuevos (ya organizados por año, ver
 * GuardaSoporteInteraccion::guardarSoporte()).
 */
class AdjuntosInteraccionController extends Controller
{
    private function auditoria(string $accion): void
    {
        app(AuditoriaController::class)->create(mb_substr($accion, 0, 250), 'ADMINISTRACIÓN');
    }

    /** "1.2 MB" / "340 KB" / "3.1 GB" — para bytes guardados en attachment_size. */
    public static function formatoTamano(?int $bytes): string
    {
        if (!$bytes) {
            return '—';
        }
        $unidades = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($unidades) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, $i === 0 ? 0 : 1).' '.$unidades[$i];
    }

    /**
     * Resumen de cuántos adjuntos hay por año, y cuánto pesan — de solo lectura, la vista previa
     * antes de poder limpiar nada (requiere admin.adjuntos.index; el botón de limpiar requiere
     * el permiso aparte admin.adjuntos.limpiar, ver la ruta). El tamaño solo se suma de lo que ya
     * se conoce (attachment_size) — lo subido antes de este cambio no lo tiene todavía; se va
     * completando solo al entrar a "Ver archivos" de cada año (ver verAnio()), sin necesidad de
     * un proceso masivo aparte.
     */
    public function index()
    {
        $porAnio = DB::table('int_seguimiento')
            ->selectRaw('YEAR(created_at) as anio, COUNT(*) as total, SUM(attachment_size) as bytes_conocidos, SUM(CASE WHEN attachment_size IS NULL THEN 1 ELSE 0 END) as sin_tamano')
            ->whereNotNull('attachment_urls')
            ->where('attachment_urls', '!=', '')
            ->groupBy('anio')
            ->orderByDesc('anio')
            ->get()
            ->map(function ($fila) {
                $fila->tamano_formateado = self::formatoTamano((int) $fila->bytes_conocidos);

                return $fila;
            });

        $anioActual = now()->year;
        $puedeLimpiar = auth()->user()->hasDirectPermission('admin.adjuntos.limpiar');

        return view('admin.adjuntos.index', compact('porAnio', 'anioActual', 'puedeLimpiar'));
    }

    /**
     * Lista, uno por uno, los adjuntos de un año — paginado para no cargar miles de golpe, con
     * filtros por fecha, tamaño y cliente (nombre o cédula). Para las filas que todavía no
     * tienen attachment_size guardado (subidas antes de este cambio), lo consulta a S3
     * (HeadObject, solo de las ~25 filas de la página actual) y lo deja guardado en la base de
     * datos de una vez — así la próxima vez que se mire esa página ya no hace falta volver a
     * preguntarle a S3.
     *
     * El filtro de tamaño solo puede mirar entre lo que YA se conoce (attachment_size no nulo):
     * no tiene sentido calcularle el tamaño a todo un año de golpe solo para poder filtrar — eso
     * sí sería la operación masiva y lenta que este diseño evita a propósito. La vista lo avisa.
     */
    public function verAnio(int $anio, Request $request)
    {
        $query = IntSeguimiento::whereYear('created_at', $anio)
            ->whereNotNull('attachment_urls')
            ->where('attachment_urls', '!=', '')
            ->with(['interaction:id,client_id', 'interaction.client:cod_ter,nom_ter', 'creator:id,name']);

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->input('desde'));
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->input('hasta'));
        }
        if ($request->filled('tamano_min')) {
            $query->whereNotNull('attachment_size')->where('attachment_size', '>=', (float) $request->input('tamano_min') * 1024 * 1024);
        }
        if ($request->filled('tamano_max')) {
            $query->whereNotNull('attachment_size')->where('attachment_size', '<=', (float) $request->input('tamano_max') * 1024 * 1024);
        }
        if ($request->filled('cliente')) {
            $buscar = $request->input('cliente');
            $query->whereHas('interaction.client', fn ($q) => $q->where('nom_ter', 'LIKE', "%{$buscar}%")->orWhere('cod_ter', 'LIKE', "%{$buscar}%"));
        }

        $seguimientos = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        foreach ($seguimientos as $seguimiento) {
            if ($seguimiento->attachment_size === null) {
                try {
                    $tamano = Storage::disk('s3')->size($seguimiento->attachment_urls);
                    $seguimiento->attachment_size = $tamano;
                    $seguimiento->saveQuietly(); // no crea un nuevo "updated_at" de gestión real
                } catch (\Throwable $e) {
                    // Archivo ya no existe en S3 (borrado manual, o de un año ya limpiado antes
                    // de que existiera este control) — se muestra "—" en vez de romper la página.
                }
            }
        }

        $anioActual = now()->year;
        $puedeLimpiar = auth()->user()->hasDirectPermission('admin.adjuntos.limpiar');

        return view('admin.adjuntos.ver-anio', compact('seguimientos', 'anio', 'anioActual', 'puedeLimpiar'));
    }

    /**
     * Borra de S3 (por lotes, hasta 1000 por llamada — el límite real de la API de S3) todos los
     * adjuntos de seguimientos creados en $anio, y deja attachment_urls en null en esas filas.
     * Nunca borra el seguimiento: la nota, el resultado y quién gestionó quedan intactos, solo
     * se pierde el archivo. Doble seguro: pide escribir el año otra vez (confirmar_anio) y no
     * deja limpiar el año en curso, para no borrar evidencia todavía activa por un clic de más.
     */
    public function limpiarAnio(Request $request, int $anio)
    {
        $request->validate(['confirmar_anio' => 'required|integer']);

        if ((int) $request->input('confirmar_anio') !== $anio) {
            return redirect()->route('admin.adjuntos.index')
                ->with('error', 'El año escrito no coincidió con el que ibas a limpiar. No se borró nada.');
        }

        if ($anio >= now()->year) {
            return redirect()->route('admin.adjuntos.index')
                ->with('error', 'Solo se pueden limpiar años anteriores al actual, para no borrar evidencia todavía en uso.');
        }

        $bucket = config('filesystems.disks.s3.bucket');
        $client = Storage::disk('s3')->getClient();
        $totalBorrados = 0;

        IntSeguimiento::whereYear('created_at', $anio)
            ->whereNotNull('attachment_urls')
            ->where('attachment_urls', '!=', '')
            ->select('id', 'attachment_urls')
            ->orderBy('id')
            ->chunkById(1000, function ($seguimientos) use (&$totalBorrados, $client, $bucket) {
                $rutas = $seguimientos->pluck('attachment_urls')->filter()->values();
                if ($rutas->isEmpty()) {
                    return;
                }

                $client->deleteObjects([
                    'Bucket' => $bucket,
                    'Delete' => ['Objects' => $rutas->map(fn ($ruta) => ['Key' => $ruta])->all()],
                ]);

                IntSeguimiento::whereIn('id', $seguimientos->pluck('id'))->update(['attachment_urls' => null]);
                $totalBorrados += $rutas->count();
            });

        $this->auditoria("Limpieza de adjuntos de Interacciones del año {$anio}: {$totalBorrados} archivos eliminados de S3 (los seguimientos se conservan, solo se quitó el archivo).");

        return redirect()->route('admin.adjuntos.index')
            ->with('success', "Se eliminaron {$totalBorrados} adjuntos del año {$anio}. Los registros de seguimiento se conservan — solo se quitó el archivo.");
    }
}
