<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeDistritos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaeDistritoController extends Controller
{
    private const CAMPOS_CARGOS = [
        'cc_supervisor',
        'cc_primer_presb',
        'cc_segundo_presb',
        'cc_tercer_presb',
        'cc_secre_presb',
        'cc_teso_presb',
        'cc_fiscal',
        'cc_asesor_corpen',
    ];

    public function index(Request $request)
    {
        $query = MaeDistritos::query()->with('supervisor')->withCount('congregaciones');
        $busqueda = trim((string) $request->input('search'));

        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda) {
                $q->where('COD_DIST', 'LIKE', "%{$busqueda}%")
                    ->orWhere('NOM_DIST', 'LIKE', "%{$busqueda}%");
            });
        }

        $distritos = $query->orderBy('COD_DIST')->paginate(15)->withQueryString();

        return view('maestras.distritos.index', compact('distritos'));
    }

    public function create()
    {
        return view('maestras.distritos.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validarDatos($request);

        MaeDistritos::create($datos);

        return redirect()->route('maestras.distrito.index')->with('success', '¡Distrito registrado exitosamente!');
    }

    public function edit(MaeDistritos $distrito)
    {
        $distrito->load(array_map(fn ($campo) => $this->relacionDeCampo($campo), self::CAMPOS_CARGOS));

        return view('maestras.distritos.edit', compact('distrito'));
    }

    public function update(Request $request, MaeDistritos $distrito)
    {
        $datos = $this->validarDatos($request, esCreacion: false);

        $distrito->update($datos);

        return redirect()->route('maestras.distrito.index')->with('success', '¡Distrito actualizado exitosamente!');
    }

    public function destroy(MaeDistritos $distrito)
    {
        try {
            $distrito->delete();

            return redirect()->route('maestras.distrito.index')->with('success', '¡Distrito eliminado exitosamente!');
        } catch (\Exception $e) {
            return redirect()->route('maestras.distrito.index')->with('error', 'No se pudo eliminar el distrito. Es posible que tenga congregaciones asociadas.');
        }
    }

    private function validarDatos(Request $request, bool $esCreacion = true): array
    {
        $reglas = [
            'cod_dist' => $esCreacion ? ['required', 'integer', Rule::unique('MaeDistritos', 'COD_DIST')] : ['required', 'integer'],
            'nom_dist' => ['required', 'string', 'max:100'],
            'detalle' => ['nullable', 'string'],
            'compuest' => ['nullable', 'string'],
        ];

        foreach (self::CAMPOS_CARGOS as $campo) {
            $reglas[$campo] = ['nullable', 'integer', Rule::exists('MaeTerceros', 'cod_ter')];
        }

        $validado = $request->validate($reglas, [], [
            'cod_dist' => 'código',
            'nom_dist' => 'nombre',
        ]);

        $datos = [
            'NOM_DIST' => strtoupper($validado['nom_dist']),
            'DETALLE' => $validado['detalle'] ?? null,
            'COMPUEST' => $validado['compuest'] ?? null,
        ];

        if ($esCreacion) {
            $datos['COD_DIST'] = $validado['cod_dist'];
        }

        foreach (self::CAMPOS_CARGOS as $campo) {
            // "nullable" no convierte '' a null, solo evita que otras reglas fallen con el campo
            // vacío — sin este cast, un campo de cargo dejado en blanco llega aquí como '' y
            // truena al insertar en una columna bigint.
            $valor = $validado[$campo] ?? null;
            $datos[$campo] = ($valor === '' || $valor === null) ? null : (int) $valor;
        }

        return $datos;
    }

    private function relacionDeCampo(string $campo): string
    {
        return [
            'cc_supervisor' => 'supervisor',
            'cc_primer_presb' => 'primerPresbitero',
            'cc_segundo_presb' => 'segundoPresbitero',
            'cc_tercer_presb' => 'tercerPresbitero',
            'cc_secre_presb' => 'secretarioPresbiterio',
            'cc_teso_presb' => 'tesoreroPresbiterio',
            'cc_fiscal' => 'fiscal',
            'cc_asesor_corpen' => 'asesorCorpen',
        ][$campo];
    }
}
