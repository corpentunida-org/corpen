<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuditoriaController;
use App\Models\Exequiales\ComaeExRelPar;
use App\Models\Exequiales\ComaeExCli;
use App\Models\Exequiales\ComaeTer;
use App\Models\Maestras\maeTerceros;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ComaeExCliController extends Controller
{
    /* public function __construct()
    {
        $this->middleware(['auth']);
    } */

    private function auditoria($accion, $area)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, $area);
    }

    public function index()
    {
        dd(getenv('API_PRODUCCION'), $_ENV['API_PRODUCCION'] ?? 'no en _ENV', $_SERVER['API_PRODUCCION'] ?? 'no en _SERVER');
        $token = env('TOKEN_ADMIN');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/District');
        if ($response->status() === 401) {
            session()->flash('warning', 'Debe cambiar el token de la API para poder usar la aplicación.');
            return view('exequial.asociados.index');
        }
        return view('exequial.asociados.index');
    }

    //Datos solo del titular
    public function titularShow($id)
    {
        //API
        $token = env('TOKEN_ADMIN');
        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);

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
        //API
        $token = env('TOKEN_ADMIN');
        $id = $request->input('id');
        $maeter = maeTerceros::where('cod_ter', $id)->exists();

        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);

        $beneficiarios = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales', [
            'documentId' => $id,
        ]);

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
            $tercero = ComaeTer::where('cod-ter', $id)->first();
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

    public function store(Request $request)
    {
        //$this->authorize('create', auth()->user());
        $token = env('TOKEN_ADMIN');
        $fechaActual = Carbon::now();
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $request->documentId,
            'obsevations' => $request->observaciones,
            'dateStart' => $fechaActual,
            'descuento' => $request->discount,
            'codePlan' => $request->plan,
            'codeCenterCost' => 'C1010',
        ]);
        ComaeExCli::create([
            'cod_cli' => $request->documentId,
            'cod_plan' => $request->plan,
            'fec_ing' => $fechaActual,
            'cod_cco' => 'C1010',
            'estado' => true,
            'fec_ini' => $fechaActual,
            'por_descto' => $request->discount,
        ]);
        if ($response->successful()) {
            $accion = 'add titular ' . $request->documentId;
            $this->auditoria($accion, 'EXEQUIALES');
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->cedulaAsociado;
            return redirect()->to($url)->with('success', 'Titular agregado exitosamente');
        } else {
            $jsonResponse = $response->json();
            $message = $jsonResponse['message'];
            return redirect()->back()->with('error', $message);
        }
    }

    public function update(Request $request)
    {
        //$this->authorize('update', auth()->user());
        $token = env('TOKEN_ADMIN');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->patch(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $request->documentid,
            'dateInit' => $request->dateInit,
            'codePlan' => $request->codePlan,
            'discount' => $request->discount,
            'observation' => $request->observation,
            'stade' => true,
        ]);
        ComaeExCli::where('cod_cli', $request->documentid)->update([
            'cod_plan' => $request->codePlan,
            'por_descto' => $request->discount,
            'benef' => $request->observation,
            'estado' => true,
        ]);

        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
        if ($response->successful()) {
            $accion = 'update titular ' . $request->documentid;
            $this->auditoria($accion, 'EXEQUIALES');
            //return $data; //antigua vista
            //plantilla
            return redirect()->to($url)->with('success', 'Titular actualizado exitosamente');
        } else {
            //return response()->json(['error' => $response->json()], $response->status());
            return redirect()
                ->to($url)
                ->with('msjerror', 'No se pudo actualizar el titular ' . $response->json());
        }
    }

    public function generarpdf($id, $active)
    {
        $token = env('TOKEN_ADMIN');

        $titular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales/Tercero', [
            'documentId' => $id,
        ]);
        $beneficiarios = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Exequiales', [
            'documentId' => $id,
        ]);
        $personalTitular = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(env('API_PRODUCCION') . '/api/Pastors', [
            'documentId' => $id,
        ]);

        if ($personalTitular->failed() || $personalTitular->status() == 500 || empty($personalTitular->json())) {
            $tercero = maeTerceros::where('cod_ter', $id)->select('cod_dist', 'fec_nac', 'congrega', 'tel', 'email')->first();
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
