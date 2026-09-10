<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exequial\StoreAsociadoRequest;
use App\Http\Requests\Exequial\UpdateAsociadoRequest;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;
use App\Models\Exequiales\ComaeExRelPar;
use App\Models\Exequiales\ComaeExCli;
use App\Models\Exequiales\ComaeTer;
use App\Models\Exequiales\TitularRetiro;
use App\Models\Maestras\MaeTerceros;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ComaeExCliController extends Controller
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
     * Listado de titulares. Es un JOIN 100% local (EXE_ExCli + MaeTerceros, que
     * cubre ~99% de los nombres vía cod_ter = cod_cli) — a propósito NO llama a
     * la API externa para navegar/buscar/paginar, para no repetir el problema de
     * N+1 llamadas HTTP que se corrigió hoy mismo en el dashboard de "Prestar
     * Servicio" (antes esta pantalla ni siquiera tenía listado, solo un buscador
     * por cédula exacta).
     *
     * No corre ningún query mientras no se aplique una búsqueda o un filtro de
     * estado: con 6.672 titulares, mostrar "los primeros 20 por cédula" al
     * entrar no aporta nada útil y da sensación de aplicación lenta al cargar
     * de una tabla completa sin que el usuario haya pedido nada todavía.
     */
    public function index(Request $request)
    {
        $busqueda = trim((string) $request->input('buscar', ''));
        $estadoFiltro = $request->input('estado');
        $sePresentoFiltro = $busqueda !== '' || in_array($estadoFiltro, ['activo', 'inactivo'], true);

        $titulares = null;
        $retirados = collect();

        if ($sePresentoFiltro) {
            // Búsqueda por nombre vía FULLTEXT (índice ft_mae_terceros_nombres, ya usado por
            // EmpleadoController::buscarTercero() con el mismo patrón) en vez de LIKE '%...%' por
            // columna: un LIKE por columna nunca hace match con un nombre completo como "miguel
            // angel torres" porque ninguna columna individual (nom1/nom2/apl1/apl2) contiene la
            // cadena completa, y además el comodín inicial no puede usar índice. Cada palabra se
            // busca como prefijo obligatorio ("+palabra*"), así que las palabras pueden estar en
            // cualquiera de las columnas del FULLTEXT y en cualquier orden.
            $terminoBooleano = collect(preg_split('/\s+/', $busqueda))
                ->filter()
                ->map(fn($palabra) => '+' . preg_replace('/[+\-><()~*"@]/', '', $palabra) . '*')
                ->filter(fn($palabra) => $palabra !== '+*')
                ->implode(' ');

            $titulares = DB::table('EXE_ExCli as e')
                ->leftJoin('MaeTerceros as t', 't.cod_ter', '=', 'e.cod_cli')
                ->select(
                    'e.cod_cli',
                    'e.estado',
                    'e.fec_ing',
                    DB::raw("TRIM(CONCAT_WS(' ', t.nom1, t.nom2, t.apl1, t.apl2)) as nombre")
                )
                ->when($busqueda !== '', function ($q) use ($busqueda, $terminoBooleano) {
                    $q->where(function ($sub) use ($busqueda, $terminoBooleano) {
                        $sub->where('e.cod_cli', 'like', "%{$busqueda}%");
                        if ($terminoBooleano !== '') {
                            $sub->orWhereRaw(
                                'MATCH(t.nom1, t.nom2, t.apl1, t.apl2, t.nom_ter) AGAINST(? IN BOOLEAN MODE)',
                                [$terminoBooleano]
                            );
                        }
                    });
                })
                ->when($estadoFiltro === 'activo', fn ($q) => $q->where('e.estado', true))
                ->when($estadoFiltro === 'inactivo', fn ($q) => $q->where('e.estado', false))
                ->orderBy('e.cod_cli')
                ->paginate(20)
                ->withQueryString();

            // Para distinguir en pantalla "Retirado" de "Inactivo" (fallecido u
            // otra razón legada) sin hacer un query por fila: un solo IN() con
            // las cédulas de la página actual.
            $cedulasEnPagina = collect($titulares->items())->pluck('cod_cli');
            $retirados = TitularRetiro::whereIn('cod_cli', $cedulasEnPagina)->vigentes()->pluck('cod_cli')->flip();
        }

        return view('exequial.asociados.index', compact('titulares', 'retirados', 'busqueda', 'estadoFiltro', 'sePresentoFiltro'));
    }

    //Datos solo del titular
    public function titularShow($id)
    {
        try {
            $titular = $this->api->get('/api/Exequiales/Tercero', ['documentId' => $id]);
        } catch (ExequialApiException $e) {
            return redirect()->route('exequial.asociados.index')->with('warning', $e->getMessage());
        }

        if ($titular->successful()) {
            return $titular->json();
        } else {
            return redirect()->route('exequial.asociados.index')->with('warning', 'No se encontró la cédula como titular de exequiales');
        }
    }

    public function edit($id)
    {
        $jsonTit = $this->titularShow($id);
        $controllerplanes = app()->make(PlanController::class);
        return view('exequial.asociados.edit', [
            'asociado' => $jsonTit,
            'plans' => $controllerplanes->index(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $id = $request->input('id');
        $maeter = MaeTerceros::where('cod_ter', $id)->exists();

        try {
            $titular = $this->api->get('/api/Exequiales/Tercero', ['documentId' => $id]);
            $beneficiarios = $this->api->get('/api/Exequiales', ['documentId' => $id]);
        } catch (ExequialApiException $e) {
            return redirect()->route('exequial.asociados.index')->with('warning', $e->getMessage());
        }

        if ($titular->successful() && $beneficiarios->successful()) {
            $jsonTit = $titular->json();
            $jsonBene = $beneficiarios->json();
            if (isset($jsonTit['codePlan'])) {
                $controllerplanes = app()->make(PlanController::class);
                $nomPlan = $controllerplanes->nomCodPlan($jsonTit['codePlan']);
                $jsonTit['codePlan'] = $nomPlan;
            }
            return view('exequial.asociados.show', [
                'asociado' => $jsonTit,
                'beneficiarios' => $jsonBene,
                'maeter' => $maeter,
            ]);
        } else {
            return redirect()->route('exequial.asociados.index')->with('warning', 'No se encontró la cédula como titular de exequiales');
        }
    }

    public function validarRegistro(Request $request)
    {
        $id = $request->input('id');
        $asociado = ComaeExCli::where('cedula', $id)->first();
        if ($asociado) {
            return '1';
        } else {
            $tercero = ComaeTer::where('cod_ter', $id)->first();
            if ($tercero) {
                return '2';
            } else {
                return '0';
            }
        }
    }

    public function create(Request $request)
    {
        $controllerplan = app()->make(PlanController::class);
        return view('exequial.asociados.create', [
            'plans' => $controllerplan->index(),
        ]);
    }

    public function store(StoreAsociadoRequest $request)
    {
        //$this->authorize('create', auth()->user());
        $fechaActual = Carbon::now();
        try {
            $response = $this->api->post('/api/Exequiales/Tercero', [
                'documentId' => $request->documentId,
                'obsevations' => $request->observaciones,
                'dateStart' => $fechaActual,
                'descuento' => $request->discount,
                'codePlan' => $request->plan,
                'codeCenterCost' => 'C1010',
            ]);
        } catch (ExequialApiException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        if ($response->successful()) {
            ComaeExCli::create([
                'cod_cli' => $request->documentId,
                'cod_plan' => $request->plan,
                'fec_ing' => $fechaActual,
                'cod_cco' => 'C1010',
                'estado' => true,
                'fec_ini' => $fechaActual,
                'por_descto' => $request->discount,
            ]);
            $accion = 'add titular ' . $request->documentId;
            $this->auditoria($accion, 'EXEQUIALES');
            // Antes usaba $request->cedulaAsociado, un campo que este formulario nunca envía
            // (ver resources/views/exequial/asociados/create.blade.php) — la redirección
            // terminaba en "...?id=" sin valor. El dato real que identifica al titular recién
            // creado es documentId.
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentId;
            return redirect()->to($url)->with('success', 'Titular agregado exitosamente');
        } else {
            $jsonResponse = $response->json();
            $message = $jsonResponse['message'] ?? 'Error desconocido al comunicarse con la API de Exequiales.';
            return redirect()->back()->with('error', $message);
        }
    }

    public function update(UpdateAsociadoRequest $request)
    {
        //$this->authorize('update', auth()->user());
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
        try {
            $response = $this->api->patch('/api/Exequiales/Tercero', [
                'documentId' => $request->documentid,
                'dateInit' => $request->dateInit,
                'codePlan' => $request->codePlan,
                'discount' => $request->discount,
                'observation' => $request->observation,
                'stade' => true,
            ]);
        } catch (ExequialApiException $e) {
            return redirect()->to($url)->with('msjerror', $e->getMessage());
        }

        if ($response->successful()) {
            ComaeExCli::where('cod_cli', $request->documentid)->update([
                'cod_plan' => $request->codePlan,
                'por_descto' => $request->discount,
                'benef' => $request->observation,
                'estado' => true,
            ]);
            $accion = 'update titular ' . $request->documentid;
            $this->auditoria($accion, 'EXEQUIALES');
            return redirect()->to($url)->with('success', 'Titular actualizado exitosamente');
        } else {
            $jsonResponse = $response->json();
            $message = $jsonResponse['message'] ?? 'Error desconocido al comunicarse con la API de Exequiales.';
            return redirect()
                ->to($url)
                ->with('msjerror', 'No se pudo actualizar el titular: ' . $message);
        }
    }

    public function generarpdf($id, $active)
    {
        try {
            $titular = $this->api->get('/api/Exequiales/Tercero', ['documentId' => $id]);
            $beneficiarios = $this->api->get('/api/Exequiales', ['documentId' => $id]);
        } catch (ExequialApiException $e) {
            return redirect()->route('exequial.asociados.index')->with('warning', $e->getMessage());
        }

        try {
            $personalTitular = $this->api->get('/api/Pastors', ['documentId' => $id]);
        } catch (ExequialApiException $e) {
            $personalTitular = null;
        }

        if (!$personalTitular || $personalTitular->failed() || $personalTitular->status() == 500 || empty($personalTitular->json())) {
            $tercero = MaeTerceros::where('cod_ter', $id)->select('cod_dist', 'fec_nac', 'congrega', 'tel', 'email')->first();
            $personalTitular = [
                'district' => $tercero && $tercero->cod_dist ? substr($tercero->cod_dist, -2) : '',
                'birthdate' => $tercero->fec_nac ? $tercero->fec_nac->format('Y-m-d\TH:i:s') : '',
                'phone' => $tercero->tel ?? '',
                'congregation' => $tercero->congrega ?? '',
                'email' => $tercero->email ?? '',
            ];
        }
        if ($titular->successful() && $beneficiarios->successful()) {
            $jsonTit = $titular->json();
            $jsonBene = $beneficiarios->json();
            if (isset($jsonTit['codePlan'])) {
                $controllerplanes = app()->make(PlanController::class);
                $nomPlan = $controllerplanes->nomCodPlan($jsonTit['codePlan']);
                $jsonTit['codePlan'] = $nomPlan;
            }

            $data = [
                'asociado' => $jsonTit,
                'beneficiarios' => $jsonBene,
                'pastor' => $personalTitular,
                'image_path' => public_path('assets/images/CORPENTUNIDA_LOGO PRINCIPAL  (2).png'),
            ];
            $isActive = filter_var($active, FILTER_VALIDATE_BOOLEAN);
            if ($isActive) {
                $pdf = Pdf::loadView('exequial.asociados.showpdf', $data)->setPaper('letter', 'landscape');
                return $pdf->download(date('Y-m-d') . ' Reporte ' . $jsonTit['documentId'] . '.pdf');
            } else {
                $pdf = Pdf::loadView('exequial.asociados.showpdf2', $data)->setPaper('letter', 'landscape');
                return $pdf->download(date('Y-m-d') . ' Reporte ' . $jsonTit['documentId'] . '.pdf');
            }
        }
    }
}
