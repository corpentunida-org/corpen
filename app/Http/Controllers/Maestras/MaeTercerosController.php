<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeTerceros;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\MaeCongregacion;
use App\Models\Maestras\MaeDistritos;
use App\Models\Soportes\ScpUsuario;
use App\Models\Interacciones\Interaction;
use App\Models\Vistas\VisitaCorpen;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MaeTercerosController extends Controller
{
    // Mismas reglas que usa SGRH para estos campos (App\Http\Controllers\Sgrh\EmpleadoController::
    // updateTercero) — se repiten aquí literal a propósito, para que las dos pantallas exijan
    // exactamente lo mismo sobre la misma tabla y no queden dos criterios distintos de qué es un
    // dato válido en MaeTerceros.
    private const REGLAS_CURADAS_SGRH = [
        'estado' => 'nullable|string|max:10',
        'apl1' => 'nullable|string|max:100',
        'apl2' => 'nullable|string|max:100',
        'nom1' => 'nullable|string|max:255',
        'nom2' => 'nullable|string|max:255',
        'sexo' => 'nullable|string|max:10',
        'fec_nac' => 'nullable|date',
        'est_civil' => 'nullable|string|max:10',
        'tipo_ter' => 'nullable|integer',
        'tip_pers' => 'nullable|string|max:10',
        'tdoc' => 'nullable|string|max:10',
        // Solo aplica (y es obligatorio) cuando el tipo de documento es NIT (31).
        'dv' => 'nullable|required_if:tdoc,31|string|max:10',
        'fec_expcc' => 'nullable|date',
        'nom_conyug' => 'nullable|string',
        'id_conyuge' => 'nullable|integer',
        'parentesco' => 'nullable|string|max:10',
        'mail_conyu' => 'nullable|email|max:100',
        'num_hijos' => 'nullable|string|max:10',
        'fec_falle' => 'nullable|date',
        'contacto' => 'nullable|string',
        'cont_tel' => 'nullable|string|max:10',
        'dir' => 'nullable|string|max:255',
        'dir1' => 'nullable|string|max:255',
        'dir2' => 'nullable|string|max:255',
        'dir_comer' => 'nullable|string|max:255',
        'ciu_comer' => 'nullable|string|max:10',
        'dpto' => 'nullable|integer',
        'mun' => 'nullable|integer',
        'pais' => 'nullable|string|max:100',
        'cod_postal' => 'nullable|string|max:10',
        'barrio' => 'nullable|string|max:255',
        'lugar_naci' => 'nullable|string',
        'lugar_expcc' => 'nullable|string',
        'tel' => 'nullable|string|max:255',
        'tel1' => 'nullable|string|max:255',
        'tel2' => 'nullable|string|max:255',
        'cel' => 'nullable|string|max:100',
        'fax1' => 'nullable|string|max:10',
        'email' => 'nullable|email|max:255',
    ];

    // Resto de columnas que aparecen en el formulario genérico (ver form.blade.php) y que SGRH
    // no cubre: nullable|string con el límite REAL de la columna en MaeTerceros (verificado
    // contra information_schema, no adivinado) — antes estos ~110 campos no tenían ninguna
    // validación y se guardaban tal cual llegaran del POST.
    private const LONGITUDES_GENERICAS = [
        'id_ter' => 255, 'digito_v' => 255, 'tip_cli' => 10, 'razon_soc' => 10, 'raz' => 10,
        'repres' => 10, 'cargo' => 100,
        'cod_activ' => 10, 'cod_act' => 10, 'Cod_acteco' => 10, 'cod_cla' => 10, 'clasific' => 10,
        'clas_cli' => 10,
        'cod_ciu' => 10, 'ciudad' => 10, 'depa' => 10, 'cod_depa' => 255, 'cod_pais' => 255,
        'codpostal' => 10, 'cod_suc' => 10, 'cod_bod' => 10, 'cod_ban' => 255, 'cod_zona' => 10,
        'cod_ven' => 10, 'cod_ven1' => 10, 'cod_ven2' => 10, 'cod_ven3' => 10, 'cod_ven4' => 10,
        'cod_lice' => 10, 'cod_clase' => 10, 'cod_est' => 10, 'cod_respfiscal' => 10, 'cod_tributo' => 10,
        'email_fac' => 100, 'email_fact' => 100, 'email_fe' => 10, 'cont_cxc' => 10, 'exten' => 10,
        'ind_cred' => 10, 'cupo_cred' => 10, 'ind_rete' => 10, 'ind_ret' => 10, 'aut_ret' => 10,
        'ind_iva' => 10, 'ind_cree' => 10, 'ind_requ' => 255, 'ind_items' => 255, 'ind_doc' => 255,
        'ind_tmk' => 10, 'indpcom' => 10, 'por_com' => 10, 'por_comi' => 10,
        'pc1' => 10, 'pc2' => 10, 'pc3' => 10, 'dp1' => 255, 'dp2' => 255, 'dp3' => 10,
        'dto_det' => 10, 'por_des' => 10, 'prec_rem' => 10, 'lista_prec' => 10, 'pla_com' => 10,
        'dia_plaz' => 10, 'dia_com' => 255, 'dia_adp' => 10, 'ind_suc' => 10, 'suc_cli' => 10,
        'cod_can' => 10, 'esp_gab' => 255, 'uni_fra' => 255, 'ind_mayor' => 10, 'r_semana' => 10,
        'pago' => 255, 'pago1' => 10,
        'cupo_cxc' => 10, 'i_cupocc' => 10, 'cupo_cxp' => 10, 'i_cupocp' => 10, 'cta' => 255,
        'cta_ban' => 10, 'cta_icap' => 10, 'cta_icac' => 10, 'por_cred' => 10, 'int_mora' => 10,
        'icrecon' => 255,
        'regimen' => 10, 'codimpuesto' => 10, 'ret_prv' => 255, 'bloqueo' => 10, 'bloq_aut' => 255,
        'bloq_tmk' => 255, 'bloq_ate' => 255, 'exo_bloq' => 10, 'ret_iva' => 255, 'rtiva' => 100,
        'ret_ica' => 255, 'rtica' => 100,
        'conta' => 10, 'inf_ter' => 255, 'matricula' => 10, 'observ' => 255, 'respon' => 10,
        'por_ica' => 10,
    ];

    // Columnas datetime reales de MaeTerceros que no están en las reglas curadas de SGRH
    // (esas ya validan fec_nac/fec_expcc/fec_falle) — fec_act es varchar pero se sigue tratando
    // como fecha porque así se diligencia en el formulario.
    private const CAMPOS_FECHA_GENERICOS = [
        'fec_ing', 'fec_cump', 'fec_act', 'fec_dat', 'fecha_lice', 'fecha_ipuc', 'fec_aport',
        'fecha_aded', 'fec_minis',
    ];

    /**
     * Reglas de validación para todo el formulario genérico de Terceros: las curadas de SGRH
     * más una regla nullable|string|max acorde al tipo real de columna para el resto. cod_ter,
     * nom_ter, tdoc, tip_pers y tip_prv NO van aquí — cada acción (store/update) define su propia
     * exigencia de obligatoriedad para esos 5 y la agrega encima de este set base.
     */
    private function reglasValidacion(): array
    {
        $reglas = self::REGLAS_CURADAS_SGRH;

        foreach (self::LONGITUDES_GENERICAS as $campo => $longitud) {
            $reglas[$campo] = "nullable|string|max:{$longitud}";
        }

        foreach (self::CAMPOS_FECHA_GENERICOS as $campo) {
            $reglas[$campo] = 'nullable|date';
        }

        return $reglas;
    }

    /**
     * Mismo criterio que SGRH (EmpleadoController::updateTercero): '' se normaliza a null para
     * que una fecha vacía no tumbe el UPDATE completo (MySQL rechaza '' en una columna DATETIME),
     * y nombres/apellidos/cónyuge/barrio siempre quedan en mayúsculas.
     */
    private function normalizarDatos(array $datos): array
    {
        $datos = array_map(fn ($v) => $v === '' ? null : $v, $datos);

        foreach (['apl1', 'apl2', 'nom1', 'nom2', 'nom_conyug', 'barrio'] as $campo) {
            if (array_key_exists($campo, $datos) && $datos[$campo] !== null) {
                $datos[$campo] = mb_strtoupper($datos[$campo], 'UTF-8');
            }
        }

        return $datos;
    }

    /**
     * Lista de terceros con búsqueda opcional.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $terceros = MaeTerceros::query()
            ->with(['congregacion', 'maeTipos', 'distrito']) // Cargar relaciones para evitar N+1
            ->when($search, function ($query, $search) {
                $query
                    ->where('nom_ter', 'like', "%{$search}%")
                    ->orWhere('razon_soc', 'like', "%{$search}%")
                    ->orWhere('raz', 'like', "%{$search}%")
                    ->orWhere('cod_ter', 'like', "%{$search}%");
            })
            ->orderBy('cod_ter', 'desc')
            ->paginate(10);

        return view('maestras.terceros.index', compact('terceros', 'search'));
    }

    /**
     * Mostrar formulario para crear un nuevo tercero.
     */
    public function create()
    {
        $tipos = MaeTipo::all();
        $congregacion = MaeCongregacion::all();
        $distritos = MaeDistritos::all();
        $tercero = new MaeTerceros(); // objeto vacío para el formulario
        return view('maestras.terceros.create', compact('tercero', 'tipos', 'congregacion', 'distritos'));
    }

    /**
     * Guardar un nuevo tercero.
     */
    public function store(Request $request)
    {
        // congrega/cod_dist ya NO se piden aquí: solo se fijan al asignar el tercero como
        // pastor de una congregación (MaeCongregacionController), para que esa sea la única
        // fuente de verdad y no se puedan desincronizar.
        $validated = $request->validate(
            array_merge($this->reglasValidacion(), [
                'cod_ter' => 'required|string|max:20|unique:MaeTerceros,cod_ter',
                'nom_ter' => 'required|string|max:255',
                'tdoc' => 'required|string|max:5',
                'tip_pers' => 'required|string|max:1',
                'tip_prv' => 'required|string',
            ]),
            [
                'tip_prv.required' => 'Por favor, selecciona un tipo.',
            ],
        );

        // Preparar datos para guardar: filtrado por fillableFields() como defensa en
        // profundidad (aunque ya vengan validados, un POST armado a mano no debe poder tocar
        // congrega/cod_dist ni ninguna columna fuera de esa lista por esta vía).
        $data = array_intersect_key($this->normalizarDatos($validated), array_flip($this->fillableFields()));
        unset($data['congrega'], $data['cod_dist']);

        // Formatear fechas si existen
        $dateFields = ['fec_nac', 'fec_minis', 'fecha_ipuc', 'fec_aport', 'fec_ing', 'fec_cump', 'fec_act', 'fec_dat', 'fec_falle', 'fecha_lice', 'fecha_aded', 'fec_expcc'];

        foreach ($dateFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d H:i:s');
            }
        }

        MaeTerceros::create($data);

        return redirect()->route('maestras.terceros.index')->with('success', 'Tercero creado correctamente.');
    }

    /**
     * Mostrar formulario de edición de tercero.
     */
    public function edit(MaeTerceros $tercero)
    {
        //dd($tercero); // Debug: Ver el tercero que se va a editar
        $tipos = MaeTipo::all();
        $congregacion = MaeCongregacion::all();
        $distritos = MaeDistritos::all();

        // Cargar relaciones necesarias
        $tercero->load(['congregacion', 'maeTipos', 'distrito', 'scpUsuarios', 'interactions', 'visitasCorpen']);

        return view('maestras.terceros.edit', compact('tercero', 'tipos', 'congregacion', 'distritos'));
    }

    /**
     * Actualizar tercero existente.
     * Soporta actualización normal y actualización vía Modal (Iframe).
     */
    public function update(Request $request, MaeTerceros $tercero)
    {
        // 'sometimes' en los 5 campos de identidad: esta acción también recibe actualizaciones
        // parciales desde el modal/iframe (ver docblock), que no reenvían el formulario completo.
        $validated = $request->validate(
            array_merge($this->reglasValidacion(), [
                'cod_ter' => 'sometimes|string|max:20|unique:MaeTerceros,cod_ter,' . $tercero->cod_ter . ',cod_ter',
                'nom_ter' => 'sometimes|required|string|max:255',
                'tdoc' => 'sometimes|required|string|max:5',
                'tip_pers' => 'sometimes|required|string|max:1',
                'tip_prv' => 'sometimes|required|string',
            ]),
        );

        // Igual que store(): '' se normaliza a null (para no tumbar un UPDATE de columna
        // DATETIME) y nombres/apellidos/cónyuge/barrio quedan en mayúsculas. Después se filtran
        // los null — una actualización parcial no debe borrar los ~140 campos que no vinieron en
        // esta petición.
        $data = collect($this->normalizarDatos($validated))->filter(fn ($v) => !is_null($v))->toArray();
        // congrega/cod_dist se fijan únicamente desde MaeCongregacionController — ver el
        // comentario equivalente en store().
        unset($data['congrega'], $data['cod_dist']);

        // Formatear fechas si existen — antes esta acción validaba que fueran fechas válidas
        // pero nunca las reformateaba, así que cualquier formato distinto a Y-m-d H:i:s (ej.
        // "15/03/1990", que el request pudiera mandar) llegaba tal cual a una columna DATETIME.
        $dateFields = ['fec_nac', 'fec_minis', 'fecha_ipuc', 'fec_aport', 'fec_ing', 'fec_cump', 'fec_act', 'fec_dat', 'fec_falle', 'fecha_lice', 'fecha_aded', 'fec_expcc'];

        foreach ($dateFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d H:i:s');
            }
        }

        $tercero->update($data);
        return redirect()->back()->with('success', 'Actualizado correctamente');
    }

    /**
     * Eliminar tercero.
     */
    public function destroy(MaeTerceros $tercero)
    {
        // Verificar si tiene relaciones que impidan eliminar
        if ($tercero->interactions()->count() > 0 || $tercero->visitasCorpen()->count() > 0 || $tercero->scpUsuarios()->count() > 0) {
            return redirect()->route('maestras.terceros.index')->with('error', 'No se puede eliminar el tercero porque tiene registros relacionados.');
        }

        $tercero->delete();
        return redirect()->route('maestras.terceros.index')->with('success', 'Tercero eliminado correctamente.');
    }

    /**
     * Mostrar tercero o generar PDF.
     */
    public function show(MaeTerceros $tercero)
    {
        // Cargar todas las relaciones necesarias
        $tercero->load(['congregacion', 'maeTipos', 'distrito', 'scpUsuarios', 'interactions', 'visitasCorpen']);

        if (request()->has('pdf')) {
            $pdf = Pdf::loadView('maestras.terceros.show', compact('tercero'))->setPaper('a4', 'portrait');
            return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
        }

        return view('maestras.terceros.show', compact('tercero'));
    }

    /**
     * Generar PDF específico.
     */
    public function generarPdf($cod_ter)
    {
        $tercero = MaeTerceros::with(['congregacion', 'maeTipos', 'distrito', 'scpUsuarios', 'interactions', 'visitasCorpen'])
            ->where('cod_ter', $cod_ter)
            ->firstOrFail();

        $pdf = Pdf::loadView('maestras.terceros.pdf', compact('tercero'))->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
    }

    /**
     * Retorna los campos fillable que se pueden guardar desde el formulario.
     */
    private function fillableFields()
    {
        return [
            // IDENTIFICACIÓN
            'id',
            'id_ter',
            'cod_ter',
            'dv',
            'digito_v',
            'tdoc',
            'tip_pers',
            'tipo_ter',
            'tip_cli',
            // estado, cod_act e ind_ret sí están en el formulario (form.blade.php) pero no
            // estaban aquí: se diligenciaban y se descartaban en silencio al guardar.
            'estado',

            // ACTIVIDAD / CLASIFICACIÓN
            'cod_activ',
            'cod_act',
            'Cod_acteco',
            'cod_cla',
            'clasific',
            'clas_cli',

            // UBICACIÓN / CÓDIGOS
            'cod_ciu',
            'ciudad',
            'mun',
            'dpto',
            'depa',
            'pais',
            'cod_depa',
            'cod_pais',
            'cod_dist',
            'cod_postal',
            'codpostal',
            'cod_suc',
            'cod_bod',
            'cod_ban',
            'cod_zona',
            'cod_ven',
            'cod_ven1',
            'cod_ven2',
            'cod_ven3',
            'cod_ven4',
            'cod_lice',
            'cod_clase',
            'cod_est',
            'cod_respfiscal',
            'cod_tributo',

            // DATOS PERSONALES
            'nom_ter',
            'apl1',
            'apl2',
            'nom1',
            'nom2',
            'raz',
            'razon_soc',
            'repres',
            'sexo',
            'lugar_naci',
            'fec_nac',
            'est_civil',

            // CONYUGE / FAMILIA
            'id_conyuge',
            'nom_conyug',
            'mail_conyu',
            'num_hijos',
            'parentesco',

            // CONTACTO
            'tel',
            'tel1',
            'tel2',
            'cel',
            'fax1',
            'email',
            'email_fac',
            'email_fact',
            'email_fe',
            'contacto',
            'cont_cxc',
            'cont_tel',

            // DOMICILIO
            'dir',
            'dir1',
            'dir2',
            'dir_comer',
            'ciu_comer',
            'barrio',
            'exten',

            // INFORMACIÓN COMERCIAL / CLIENTE
            'tip_prv',
            'ind_cred',
            'cupo_cred',
            'ind_rete',
            'ind_ret',
            'aut_ret',
            'ind_iva',
            'ind_cree',
            'ind_requ',
            'ind_items',
            'ind_doc',
            'ind_tmk',
            'indpcom',
            'por_com',
            'por_comi',
            'pc1',
            'pc2',
            'pc3',
            'dp1',
            'dp2',
            'dp3',
            'dto_det',
            'por_des',
            'prec_rem',
            'lista_prec',
            'pla_com',
            'dia_plaz',
            'dia_com',
            'dia_adp',
            'ind_suc',
            'suc_cli',
            'cod_can',
            'esp_gab',
            'uni_fra',
            'ind_mayor',
            'r_semana',
            'pago',
            'pago1',

            // INFORMACIÓN FINANCIERA
            'cupo_cxc',
            'i_cupocc',
            'cupo_cxp',
            'i_cupocp',
            'cta',
            'cta_ban',
            'cta_icap',
            'cta_icac',
            'por_cred',
            'int_mora',
            'icrecon',

            // FECHAS IMPORTANTES
            'fec_ing',
            'fec_cump',
            'fec_act',
            'fec_dat',
            'fec_falle',
            'fecha_lice',
            'fecha_ipuc',
            'fec_aport',
            'fec_expcc',
            'fecha_aded',
            'fec_minis',

            // IMPUESTOS / RETENCIONES
            'regimen',
            'codimpuesto',
            'ret_prv',
            'bloqueo',
            'bloq_aut',
            'bloq_tmk',
            'bloq_ate',
            'exo_bloq',
            'ret_iva',
            'rtiva',
            'ret_ica',
            'rtica',

            // OTROS
            'cargo',
            'congrega',
            'conta',
            'inf_ter',
            'matricula',
            'observ',
            'lugar_expcc',
            'respon',
            'por_ica',
        ];
    }
}
