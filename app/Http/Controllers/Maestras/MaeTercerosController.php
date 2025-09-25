<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use App\Models\Maestras\MaeTipo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MaeTercerosController extends Controller
{
    /**
     * Lista de terceros con búsqueda opcional.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $terceros = maeTerceros::query()
            ->when($search, function ($query, $search) {
                $query->where('nom_ter', 'like', "%{$search}%")
                      ->orWhere('razon_soc', 'like', "%{$search}%")
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
        $tercero = new maeTerceros(); // objeto vacío para el formulario
        return view('maestras.terceros.create', compact('tercero','tipos'));
    }

    /**
     * Guardar un nuevo tercero.
     */
    public function store(Request $request)
    {
        // Validación mínima: solo cédula y nombre
        $request->validate([
            'cod_ter' => 'required|string|max:20|unique:MaeTerceros,cod_ter',
            'nom_ter' => 'required|string|max:255',
        ]);

        // Guardar solo los campos que existen en la tabla y llegan del request
        $data = $request->only($this->fillableFields());

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
        return view('maestras.terceros.edit', compact('tercero','tipos'));
    }

    /**
     * Actualizar tercero existente.
     */
    public function update(Request $request, maeTerceros $tercero)
    {
        // Validación flexible
        $request->validate([
            'cod_ter' => "sometimes|required|string|max:20|unique:MaeTerceros,cod_ter,{$tercero->cod_ter},cod_ter",
            'nom_ter' => 'sometimes|required|string|max:255',
        ]);

        $data = $request->only($this->fillableFields());
        $tercero->update($data);

        return redirect()->route('maestras.terceros.index')
                         ->with('success', 'Tercero actualizado correctamente.');
    }

    /**
     * Eliminar tercero.
     */
    public function destroy(maeTerceros $tercero)
    {
        $tercero->delete();
        return redirect()->route('maestras.terceros.index')
                         ->with('success', 'Tercero eliminado correctamente.');
    }

    /**
     * Mostrar tercero o generar PDF.
     */
    public function show(maeTerceros $tercero)
    {
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
        $tercero = maeTerceros::with('congregaciones')
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
            'cod_ter', 'nom_ter', 'estado', 'apl1', 'apl2', 'nom1', 'nom2', 'apell1', 'apell2',
            'sexo', 'fec_nac', 'est_civil', 'tipo_ter', 'tip_pers', 'tdoc', 'dv', 'digito_v', 'razon_soc',
            'raz', 'nom_conyug', 'id_conyuge', 'parentezco', 'mail_conyu', 'num_hijos', 'fec_falle',
            'contacto', 'cont_tel', 'cargo', 'dir', 'dir1', 'dir2', 'dir_comer', 'ciu_comer', 'ciudad',
            'dpto', 'mun', 'pais', 'cod_postal', 'cod_pais', 'cod_depa', 'barrio', 'lugar_naci', 'lugar_expcc',
            'tel', 'tel1', 'tel2', 'cel', 'fax1', 'email', 'email_fe', 'email_fac',
            'fecha_lice', 'fecha_ipuc', 'fecha_aded', 'fec_aport', 'fec_cump', 'fec_expcc', 'congrega',
            'respon', 'regimen', 'cod_lice', 'cod_clase', 'cupo_cred', 'ind_cred', 'ind_rete', 'ind_requ',
            'ind_items', 'bloqueo', 'bloq_aut', 'bloq_tmk', 'bloq_ate', 'cta', 'cta_ban', 'cta_icap', 'cta_icac',
            'cod_ban', 'por_cred', 'pla_com', 'por_com', 'por_comi', 'por_des', 'cupo_cxc', 'i_cupocc',
            'i_cupocp', 'cupo_cxp', 'int_mora', 'dia_plaz', 'dia_com', 'dia_adp', 'prec_rem', 'lista_prec',
            'icrecon', 'clasific', 'cod_can', 'cod_ven', 'cod_ven1', 'cod_ven2', 'cod_ven3', 'cod_ven4',
            'cod_zona', 'cod_activ', 'cod_act', 'cod_cla', 'tip_cli', 'clas_cli', 'esp_gab', 'conta',
            'uni_fra', 'dto_det', 'ind_mayor', 'ind_iva', 'ind_ret', 'ind_doc', 'indpcom', 'ind_tmk', 'ind_cree',
            'aut_ret', 'ret_iva', 'ret_ica', 'cod_respfiscal', 'cod_tributo', 'codimpuesto', 'codpostal',
            'Cod_acteco', 'fec_minis', 'cod_dist', 'cod_est', 'inf_ter', 'observ', 'matricula', 'exten', 'dp1',
            'dp2', 'dp3', 'pc1', 'pc2', 'pc3', 'r_semana', 'pago', 'pago1', 'suc_cli', 'cod_suc', 'fecha_aded'
        ];
    }
}
