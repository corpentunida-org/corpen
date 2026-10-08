<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitudRequest;
use App\Models\Creditos\CatEgreso;
use App\Models\Creditos\CatIngreso;
use App\Models\Creditos\MovimientoGasto;
use App\Models\Creditos\MovimientoIngreso;
use App\Models\Creditos\Solicitud;
use App\Models\Maestras\MaeTerceros;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    /**
     * Formulario de "Solicitud de Crédito de Alta Cuantía", digitalizado a partir del formato
     * físico. Guarda en cre_legacy_solicitudes (origen='app') para que el historial del
     * asociado (CreditoController::historial) la muestre junto con las importadas, sin
     * necesidad de unir dos tablas distintas.
     */
    public function create()
    {
        $catIngresos = CatIngreso::orderBy('orden')->get();
        $catEgresos = CatEgreso::orderBy('orden')->get();

        return view('creditos.solicitudes.crear', compact('catIngresos', 'catEgresos'));
    }

    /**
     * Datos actuales del tercero para precargar el formulario al seleccionarlo (AJAX).
     */
    public function datosTercero(MaeTerceros $tercero)
    {
        return response()->json([
            'cod_ter' => $tercero->cod_ter,
            'nom_ter' => $tercero->nom_ter,
            'fec_nac' => optional($tercero->fec_nac)->format('Y-m-d'),
            'edad' => $tercero->fec_nac ? $tercero->fec_nac->age : null,
            'cel' => $tercero->cel,
            'whatsapp' => $tercero->whatsapp,
            'dir' => $tercero->dir,
            'ciudad' => $tercero->ciudad,
            'depa' => $tercero->depa,
            'email' => $tercero->email,
            'congrega' => $tercero->congrega,
            'cod_dist' => $tercero->cod_dist,
            // fec_ing no está en $casts de MaeTerceros (se usa en otros lados como string), así
            // que optional()->format() no sirve aquí: viene crudo desde la BD.
            'fec_ing' => $tercero->fec_ing ? \Carbon\Carbon::parse($tercero->fec_ing)->format('Y-m-d') : null,
            'fec_minis' => optional($tercero->fec_minis)->format('Y-m-d'),
            'peso' => $tercero->peso,
            'estatura' => $tercero->estatura,
            'eps' => $tercero->eps,
            'detalle_enfermedades' => $tercero->detalle_enfermedades,
            'nom_conyug' => $tercero->nom_conyug,
            'id_conyuge' => $tercero->id_conyuge,
            'cel_conyu' => $tercero->cel_conyu,
            'num_hijos' => $tercero->num_hijos,
            'personas_cargo' => $tercero->personas_cargo,
            'tipo_vivienda' => $tercero->tipo_vivienda,
            'congregacion_paga_servicios' => (bool) $tercero->congregacion_paga_servicios,
            'congregacion_paga_arriendo' => (bool) $tercero->congregacion_paga_arriendo,
            'congregacion_paga_otros' => $tercero->congregacion_paga_otros,
        ]);
    }

    public function store(StoreSolicitudRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            // 1. Actualiza los datos personales/familiares/salud del tercero (si se diligenciaron)
            $tercero = MaeTerceros::where('cod_ter', $data['cod_ter'])->first();
            $camposTercero = array_filter([
                'whatsapp' => $data['whatsapp'] ?? null,
                'cel' => $data['cel'] ?? null,
                'dir' => $data['dir'] ?? null,
                'ciudad' => $data['ciudad'] ?? null,
                'depa' => $data['depa'] ?? null,
                'email' => $data['email'] ?? null,
                'fec_ing' => $data['fec_ing'] ?? null,
                'fec_minis' => $data['fec_minis'] ?? null,
                'peso' => $data['peso'] ?? null,
                'estatura' => $data['estatura'] ?? null,
                'eps' => $data['eps'] ?? null,
                'detalle_enfermedades' => $data['detalle_enfermedades'] ?? null,
                'nom_conyug' => $data['nom_conyug'] ?? null,
                'id_conyuge' => $data['id_conyuge'] ?? null,
                'cel_conyu' => $data['cel_conyu'] ?? null,
                'num_hijos' => $data['num_hijos'] ?? null,
                'personas_cargo' => $data['personas_cargo'] ?? null,
                'tipo_vivienda' => $data['tipo_vivienda'] ?? null,
            ], fn ($v) => $v !== null && $v !== '');
            // Los checkbox de "quién paga" sí se guardan aunque vengan en false/null explícito.
            $camposTercero['congregacion_paga_servicios'] = $request->boolean('congregacion_paga_servicios');
            $camposTercero['congregacion_paga_arriendo'] = $request->boolean('congregacion_paga_arriendo');
            if (!empty($data['congregacion_paga_otros'])) {
                $camposTercero['congregacion_paga_otros'] = $data['congregacion_paga_otros'];
            }
            if (!empty($camposTercero)) {
                $tercero->update($camposTercero);
            }

            // 2. Sube el formulario firmado (PDF o foto)
            $file = $request->file('formulario_firmado');
            $cleanName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
            $filename = time() . '_' . $cleanName . '.' . $file->getClientOriginalExtension();
            $folderPath = 'corpentunida/creditos/solicitudes/' . $data['cod_ter'] . '/' . now()->format('Y/m');
            $rutaFormulario = Storage::disk('s3')->putFileAs($folderPath, $file, $filename);

            // 3. Crea la solicitud. num_soli depende del id autoincremental, así que se fija
            // después del insert con una clave temporal única para no violar el unique.
            $plazoMeses = $data['tipo_cred'] === 'HIP' ? ((int) $data['plazo']) * 12 : (int) $data['plazo'];
            $solicitud = Solicitud::create([
                'origen' => 'app',
                'num_soli' => 'TMP-' . Str::random(14), // cabe en varchar(20); se reemplaza abajo
                'fecha' => now()->toDateString(),
                'doc_deu' => $data['cod_ter'],
                'tipo_cred' => $data['tipo_cred'],
                'tipo_cuota' => $data['tipo_cuota'],
                'cod_congre' => $tercero->congrega,
                'vr_soli' => $data['vr_soli'],
                'plazo' => $plazoMeses,
                'destino' => $data['destino'],
                'tiene_credito_actual' => $request->boolean('tiene_credito_actual'),
                'cual_credito_actual' => $data['cual_credito_actual'] ?? null,
                'ruta_formulario_firmado' => $rutaFormulario,
                'cod_dist' => $tercero->cod_dist,
                'protec_dato' => '1',
            ]);
            $solicitud->update(['num_soli' => 'APP-' . str_pad($solicitud->id, 6, '0', STR_PAD_LEFT)]);

            // 4. Desglose de ingresos y egresos
            foreach ($data['ingresos'] ?? [] as $catId => $valor) {
                if ($valor === null || $valor === '') {
                    continue;
                }
                MovimientoIngreso::create([
                    'num_soli' => $solicitud->num_soli,
                    'cat_ingreso_id' => $catId,
                    'valor_ing' => $valor,
                ]);
            }
            foreach ($data['egresos'] ?? [] as $catId => $valor) {
                if ($valor === null || $valor === '') {
                    continue;
                }
                MovimientoGasto::create([
                    'num_soli' => $solicitud->num_soli,
                    'cat_egreso_id' => $catId,
                    'valor_egre' => $valor,
                ]);
            }

            // 5. Totales de ingresos/egresos en la solicitud misma (igual que el archivo legacy)
            $solicitud->update([
                'ingresos' => array_sum(array_filter($data['ingresos'] ?? [])),
                'egresos' => array_sum(array_filter($data['egresos'] ?? [])),
            ]);
        });

        return redirect()
            ->route('creditos.credito.historial', ['cedula' => $data['cod_ter']])
            ->with('success', 'Solicitud registrada correctamente.');
    }
}
