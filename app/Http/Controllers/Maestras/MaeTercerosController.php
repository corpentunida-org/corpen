<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\MaeTipo;
use App\Models\Maestras\Congregacion;
use App\Models\Maestras\maeDistritos;
use App\Models\Soportes\ScpUsuario;
use App\Models\Interacciones\Interaction;
use App\Models\Vistas\VisitaCorpen;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MaeTercerosController extends Controller
{
    /**
     * Lista de terceros con búsqueda opcional.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $terceros = maeTerceros::query()
            ->with(['congregaciones', 'maeTipos', 'distrito']) // Cargar relaciones para evitar N+1
            ->when($search, function ($query, $search) {
                $query->where('nom_ter', 'like', "%{$search}%")
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
        $congregaciones = Congregacion::all();
        $distritos = maeDistritos::all();
        $tercero = new maeTerceros(); // objeto vacío para el formulario
        return view('maestras.terceros.create', compact('tercero', 'tipos', 'congregaciones', 'distritos'));
    }

    /**
     * Guardar un nuevo tercero.
     */
    public function store(Request $request)
    {
        // Validación mejorada según los campos del modelo
        $request->validate([
            'cod_ter' => 'required|string|max:20|unique:MaeTerceros,cod_ter',
            'nom_ter' => 'required|string|max:255',
            'tdoc' => 'required|string|max:5',
            'tip_pers' => 'required|string|max:1',
            'email' => 'nullable|email',
            'fec_nac' => 'nullable|date',
            'fec_minis' => 'nullable|date',
            'fecha_ipuc' => 'nullable|date',
            'fec_aport' => 'nullable|date',
        ]);

        // Preparar datos para guardar
        $data = $request->only($this->fillableFields());
        
        // Formatear fechas si existen
        $dateFields = ['fec_nac', 'fec_minis', 'fecha_ipuc', 'fec_aport', 'fec_ing', 'fec_cump', 
                       'fec_act', 'fec_dat', 'fec_falle', 'fecha_lice', 'fecha_aded', 'fec_expcc'];
        
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d H:i:s');
            }
        }

        maeTerceros::create($data);

        return redirect()
            ->route('maestras.terceros.index')
            ->with('success', 'Tercero creado correctamente.');
    }

    /**
     * Mostrar formulario de edición de tercero.
     */
    public function edit(maeTerceros $tercero)
    {
        $tipos = MaeTipo::all();
        $congregaciones = Congregacion::all();
        $distritos = maeDistritos::all();
        
        // Cargar relaciones necesarias
        $tercero->load(['congregaciones', 'maeTipos', 'distrito', 'scpUsuarios', 
                        'interactions', 'visitasCorpen']);
        
        return view('maestras.terceros.edit', compact('tercero', 'tipos', 'congregaciones', 'distritos'));
    }

    /**
     * Actualizar tercero existente.
     */
    public function update(Request $request, maeTerceros $tercero)
    {
        // Validación mejorada
        $request->validate([
            'cod_ter' => "sometimes|required|string|max:20|unique:MaeTerceros,cod_ter,{$tercero->cod_ter},cod_ter",
            'nom_ter' => 'sometimes|required|string|max:255',
            'tdoc' => 'sometimes|required|string|max:5',
            'tip_pers' => 'sometimes|required|string|max:1',
            'email' => 'nullable|email',
            'fec_nac' => 'nullable|date',
            'fec_minis' => 'nullable|date',
            'fecha_ipuc' => 'nullable|date',
            'fec_aport' => 'nullable|date',
        ]);

        $data = $request->only($this->fillableFields());
        
        // Formatear fechas si existen
        $dateFields = ['fec_nac', 'fec_minis', 'fecha_ipuc', 'fec_aport', 'fec_ing', 'fec_cump', 
                       'fec_act', 'fec_dat', 'fec_falle', 'fecha_lice', 'fecha_aded', 'fec_expcc'];
        
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field])->format('Y-m-d H:i:s');
            }
        }
        
        $tercero->update($data);

        return redirect()->route('maestras.terceros.index')
                         ->with('success', 'Tercero actualizado correctamente.');
    }

    /**
     * Eliminar tercero.
     */
    public function destroy(maeTerceros $tercero)
    {
        // Verificar si tiene relaciones que impidan eliminar
        if ($tercero->interactions()->count() > 0 || 
            $tercero->visitasCorpen()->count() > 0 || 
            $tercero->scpUsuarios()->count() > 0) {
            return redirect()->route('maestras.terceros.index')
                             ->with('error', 'No se puede eliminar el tercero porque tiene registros relacionados.');
        }
        
        $tercero->delete();
        return redirect()->route('maestras.terceros.index')
                         ->with('success', 'Tercero eliminado correctamente.');
    }

    /**
     * Mostrar tercero o generar PDF.
     */
    public function show(maeTerceros $tercero)
    {
        // Cargar todas las relaciones necesarias
        $tercero->load(['congregaciones', 'maeTipos', 'distrito', 'scpUsuarios', 
                        'interactions', 'visitasCorpen']);

        if (request()->has('pdf')) {
            $pdf = Pdf::loadView('maestras.terceros.show', compact('tercero'))
                      ->setPaper('a4', 'portrait');
            return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
        }

        return view('maestras.terceros.show', compact('tercero'));
    }

    /**
     * Generar PDF específico.
     */
    public function generarPdf($cod_ter)
    {
        $tercero = maeTerceros::with(['congregaciones', 'maeTipos', 'distrito', 'scpUsuarios', 
                                      'interactions', 'visitasCorpen'])
                    ->where('cod_ter', $cod_ter)
                    ->firstOrFail();

        $pdf = Pdf::loadView('maestras.terceros.pdf', compact('tercero'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Tercero_' . $tercero->cod_ter . '.pdf');
    }

    /**
     * Retorna los campos fillable que se pueden guardar desde el formulario.
     */
    private function fillableFields()
    {
        return [
            // IDENTIFICACIÓN
            'id', 'id_ter', 'cod_ter', 'dv', 'digito_v', 'tdoc', 'tip_pers', 'tipo_ter', 'tip_cli',
            
            // ACTIVIDAD / CLASIFICACIÓN
            'cod_activ', 'Cod_acteco', 'cod_cla', 'clasific', 'clas_cli',
            
            // UBICACIÓN / CÓDIGOS
            'cod_ciu', 'ciudad', 'mun', 'dpto', 'depa', 'pais', 'cod_depa', 'cod_pais', 
            'cod_dist', 'cod_postal', 'codpostal', 'cod_suc', 'cod_bod', 'cod_ban', 
            'cod_zona', 'cod_ven', 'cod_ven1', 'cod_ven2', 'cod_ven3', 'cod_ven4', 
            'cod_lice', 'cod_clase', 'cod_est', 'cod_respfiscal', 'cod_tributo',
            
            // DATOS PERSONALES
            'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'raz', 'razon_soc', 'repres', 
            'sexo', 'lugar_naci', 'fec_nac', 'est_civil',
            
            // CONYUGE / FAMILIA
            'id_conyuge', 'nom_conyug', 'mail_conyu', 'num_hijos', 'parentesco',
            
            // CONTACTO
            'tel', 'tel1', 'tel2', 'cel', 'fax1', 'email', 'email_fac', 'email_fact', 
            'email_fe', 'contacto', 'cont_cxc', 'cont_tel',
            
            // DOMICILIO
            'dir', 'dir1', 'dir2', 'dir_comer', 'ciu_comer', 'barrio', 'exten',
            
            // INFORMACIÓN COMERCIAL / CLIENTE
            'tip_prv', 'ind_cred', 'cupo_cred', 'ind_rete', 'aut_ret', 'ind_iva', 
            'ind_cree', 'ind_requ', 'ind_items', 'ind_doc', 'ind_tmk', 'indpcom', 
            'por_com', 'por_comi', 'pc1', 'pc2', 'pc3', 'dp1', 'dp2', 'dp3', 
            'dto_det', 'por_des', 'prec_rem', 'lista_prec', 'pla_com', 'dia_plaz', 
            'dia_com', 'dia_adp', 'ind_suc', 'suc_cli', 'cod_can', 'esp_gab', 
            'uni_fra', 'ind_mayor', 'r_semana', 'pago', 'pago1',
            
            // INFORMACIÓN FINANCIERA
            'cupo_cxc', 'i_cupocc', 'cupo_cxp', 'i_cupocp', 'cta', 'cta_ban', 
            'cta_icap', 'cta_icac', 'por_cred', 'int_mora', 'icrecon',
            
            // FECHAS IMPORTANTES
            'fec_ing', 'fec_cump', 'fec_act', 'fec_dat', 'fec_falle', 'fecha_lice', 
            'fecha_ipuc', 'fec_aport', 'fec_expcc', 'fecha_aded', 'fec_minis',
            
            // IMPUESTOS / RETENCIONES
            'regimen', 'codimpuesto', 'ret_prv', 'bloqueo', 'bloq_aut', 'bloq_tmk', 
            'bloq_ate', 'exo_bloq', 'ret_iva', 'rtiva', 'ret_ica', 'rtica',
            
            // OTROS
            'cargo', 'congrega', 'conta', 'inf_ter', 'matricula', 'observ', 
            'lugar_expcc', 'respon', 'por_ica',
        ];
    }
}