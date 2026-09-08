<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exequial\StoreRetiroRequest;
use App\Imports\ExcelExport;
use App\Models\Exequiales\ComaeExCli;
use App\Models\Exequiales\TitularRetiro;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RetiroTitularController extends Controller
{
    public function __construct(private ExequialApiService $api)
    {
    }

    private function auditoria($accion, $area)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, $area);
    }

    /**
     * Query base compartida entre la pantalla y las dos exportaciones, para no
     * duplicar los filtros en tres sitios distintos.
     */
    private function filtrar(Request $request)
    {
        return TitularRetiro::query()
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha_retiro', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha_retiro', '<=', $request->fecha_hasta))
            ->when($request->filled('reportado') && $request->reportado !== 'todos', function ($q) use ($request) {
                $q->where('reportado_aliado', $request->reportado === 'si');
            })
            ->orderByDesc('fecha_retiro');
    }

    public function index(Request $request)
    {
        $retiros = $this->filtrar($request)->paginate(20)->withQueryString();
        return view('exequial.retiros.index', compact('retiros'));
    }

    /**
     * Registra el retiro de un titular: se marca en la API externa (mismo patrón
     * que MaeC_ExSerController::store() usa para "pastor fallecido" — un PATCH a
     * /api/Exequiales/Tercero con stade=false) y, solo si esa llamada responde
     * exitosa, se actualiza el estado local y se crea el registro de retiro. Si
     * la API falla, no queda nada a medias (mismo criterio corregido hoy en el
     * resto del módulo).
     */
    public function store(StoreRetiroRequest $request, $cedula)
    {
        $titularLocal = ComaeExCli::where('cod_cli', $cedula)->first();
        if (!$titularLocal) {
            return redirect()->back()->with('error', 'El titular no existe localmente.');
        }
        if (!$titularLocal->estado) {
            return redirect()->back()->with('error', 'Este titular ya está inactivo.');
        }

        try {
            $titular = $this->api->get('/api/Exequiales/Tercero', ['documentId' => $cedula]);
            if (!$titular->successful()) {
                return redirect()->back()->with('error', 'No se encontró el titular en la API externa.');
            }
            $datos = $titular->json();

            $response = $this->api->patch('/api/Exequiales/Tercero', [
                'documentId' => $cedula,
                'dateInit' => $datos['dateInit'] ?? ' ',
                'codePlan' => $datos['codePlan'] ?? '01',
                'discount' => $datos['discount'] ?? 0,
                'observation' => 'RETIRO: ' . $request->observaciones,
                'stade' => false,
            ]);
        } catch (ExequialApiException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'No se pudo retirar el titular en el sistema externo.');
        }

        $titularLocal->update(['estado' => false]);

        TitularRetiro::create([
            'cod_cli' => $cedula,
            'nombre' => $datos['name'] ?? null,
            'fecha_afiliacion' => $titularLocal->fec_ing,
            'fecha_retiro' => $request->fecha_retiro,
            'observaciones' => $request->observaciones,
            'user_id' => auth()->id(),
        ]);

        $this->auditoria('retiro de titular ' . $cedula, 'EXEQUIALES');

        return redirect()->route('exequial.asociados.index')->with('success', 'Titular retirado exitosamente.');
    }

    public function marcarReportado(TitularRetiro $retiro)
    {
        $retiro->update([
            'reportado_aliado' => true,
            'reportado_en' => now(),
            'reportado_por' => auth()->id(),
        ]);

        $this->auditoria('marcar reportado al aliado el retiro de ' . $retiro->cod_cli, 'EXEQUIALES');

        return redirect()->back()->with('success', 'Marcado como reportado al aliado comercial.');
    }

    public function exportarExcel(Request $request)
    {
        $retiros = $this->filtrar($request)->get();

        $data = $retiros->map(fn ($r) => [
            $r->cod_cli,
            $r->nombre,
            optional($r->fecha_afiliacion)->format('Y-m-d'),
            $r->fecha_retiro->format('Y-m-d'),
            $r->observaciones,
            $r->reportado_aliado ? 'Sí' : 'No',
        ])->toArray();

        $headings = ['Cédula', 'Nombre', 'Fecha Afiliación', 'Fecha Retiro', 'Observaciones', 'Reportado'];
        $name = 'Retirados_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new ExcelExport($data, $headings), $name);
    }

    public function exportarPdf(Request $request)
    {
        $retiros = $this->filtrar($request)->get();

        $pdf = Pdf::loadView('exequial.retiros.pdf', [
            'retiros' => $retiros,
            'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png'),
        ])->setPaper('letter', 'landscape');

        return $pdf->download('Retirados_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
