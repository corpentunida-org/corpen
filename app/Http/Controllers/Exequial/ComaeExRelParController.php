<?php

namespace App\Http\Controllers\Exequial;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Exequial\ComaeExCliController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exequial\StoreBeneficiarioRequest;
use App\Http\Requests\Exequial\UpdateBeneficiarioRequest;
use App\Models\Exequiales\ComaeExRelPar;
use App\Services\Exequial\ExequialApiException;
use App\Services\Exequial\ExequialApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComaeExRelParController extends Controller
{
    public function __construct(private ExequialApiService $api)
    {
    }

    private function auditoria($accion, $area)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, $area);
    }

    public function edit($id, Request $request)
    {
        $idTit = $request->input('asociadoid');
        $beneficiario = [
            'name' => $request->input('name'),
            'relationship' => $request->input('relationship'),
            'dateBirthday' => substr($request->input('dateBirthday'), 0, 10),
        ];
        $controllerTit = app()->make(ComaeExCliController::class);
        $controllerparen = app()->make(ParentescosController::class);
        return view('exequial.beneficiarios.edit', [
            'id' => $id,
            'asociado' => $controllerTit->titularShow($idTit),
            'parentescos' => $controllerparen->index(),
            'beneficiario' => $beneficiario,
        ]);
    }

    public function create(Request $request)
    {
        //cedula nombre de titular
        $asociado = $request->input('asociado');
        $controllerTit = app()->make(ComaeExCliController::class);
        $controllerparen = app()->make(ParentescosController::class);
        //datos del titular
        $jsonTitular = $controllerTit->titularShow($asociado);
        //dd($asociado);
        return view('exequial.beneficiarios.create', [
            'parentescos' => $controllerparen->index(),
            'asociado' => $jsonTitular,
        ]);
    }

    public function store(StoreBeneficiarioRequest $request)
    {
        $fechaActual = Carbon::now();
        try {
            $response = $this->api->post('/api/Exequiales/Beneficiary', [
                'documentBeneficiaryId' => $request->documentid,
                'codePastor' => $request->cedulaAsociado,
                'name' => $request->apellidos . ' ' . $request->nombres,
                'dateBirthDate' => $request->fechaNacimiento ?? Carbon::now(),
                'dateEntry' => $fechaActual,
                'codeParentesco' => $request->codePar,
                'type' => 'A',
            ]);
        } catch (ExequialApiException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        if ($response->successful()) {
            ComaeExRelPar::create([
                'cedula' => $request->documentid,
                'nombre' => strtoupper($request->apellidos . ' ' . $request->nombres),
                'cod_par' => $request->codePar,
                'tipo' => 'A',
                'fec_ing' => $fechaActual,
                'fec_nac' => $request->fechaNacimiento ?? Carbon::now(),
                'estado' => true,
                'cod_cli' => $request->cedulaAsociado,
            ]);
            $accion = 'add beneficiario ' . $request->documentid;
            $this->auditoria($accion, 'EXEQUIALES');
            $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->cedulaAsociado;
            return redirect()->to($url)->with('success', 'Beneficiario agregado exitosamente');
        } else {
            return redirect()->back()->with('error', json_encode($response->json()) . ' | Estado: ' . $response->status());
        }
    }

    public function update(UpdateBeneficiarioRequest $request)
    {
        //$this->authorize('update', auth()->user());
        $fechaActual = Carbon::now();
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
        try {
            $response = $this->api->put('/api/Exequiales/Beneficiary', [
                'name' => $request->names,
                'codeParentesco' => $request->parentesco,
                'type' => 'A',
                'dateEntry' => $fechaActual,
                'documentBeneficiaryId' => $request->cedula,
                'dateBirthDate' => $request->fechaNacimiento,
            ]);
        } catch (ExequialApiException $e) {
            return redirect()->to($url)->with('msjerror', $e->getMessage());
        }
        if ($response->successful()) {
            ComaeExRelPar::where('cedula', $request->cedula)->update([
                'nombre' => strtoupper($request->names),
                'cod_par' => $request->parentesco,
                'fec_nac' => $request->fechaNacimiento,
            ]);
            $accion = 'update beneficiario ' . $request->cedula;
            $this->auditoria($accion, 'EXEQUIALES');
            return redirect()->to($url)->with('success', 'Beneficiario actualizado exitosamente');
        } else {
            return redirect()
                ->to($url)
                ->with('msjerror', 'No se pudo actualizar el titular: ' . json_encode($response->json()));
        }
    }

    public function destroy(Request $request)
    {
        //$this->authorize('delete', auth()->user());
        $id = $request->id;
        $url = route('exequial.asociados.show', ['asociado' => 'ID']) . '?id=' . $request->documentid;
        try {
            $response = $this->api->delete('/api/Exequiales/Beneficiary?idUser=' . $id);
        } catch (ExequialApiException $e) {
            return redirect()->to($url)->with('msjerror', $e->getMessage());
        }
        if ($response->successful()) {
            ComaeExRelPar::where('cedula', $request->cedula)->update([
                'estado' => false,
            ]);
            $accion = 'delete beneficiario ' . $request->beneid;
            $this->auditoria($accion, 'EXEQUIALES');
            return redirect()->to($url)->with('success', 'Beneficiario eliminado exitosamente');
        } else {
            return redirect()
                ->to($url)
                ->with('msjerror', 'No se pudo eliminar el titular: ' . json_encode($response->json()));
        }
    }
}
