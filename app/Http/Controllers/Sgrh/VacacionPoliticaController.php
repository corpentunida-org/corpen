<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\VacacionPolitica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VacacionPoliticaController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function index()
    {
        $politicas = VacacionPolitica::with('usuario')->latest('vigente_desde')->latest('id')->paginate(20);

        return view('sgrh.vacacion.politica.index', compact('politicas'));
    }

    /**
     * Una política nunca se edita in-place (mismo criterio que sgrh_contrato_modificaciones):
     * crear una nueva versión desactiva la anterior, así el histórico de qué regla aplicaba en
     * cada momento queda intacto para cálculos de saldo pasados.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dias_por_anio' => 'required|integer|min:1|max:60',
            'tipo_dias' => 'required|in:habiles,calendario',
            'max_dias_acumulables' => 'nullable|integer|min:0|max:255',
            'permite_adelanto' => 'nullable|boolean',
            'max_dias_adelanto' => 'nullable|integer|min:0|max:255',
            'vigente_desde' => 'required|date',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $validated['permite_adelanto'] = $request->boolean('permite_adelanto');
        $validated['user_id'] = Auth::id();
        $validated['activa'] = true;

        DB::transaction(function () use ($validated) {
            VacacionPolitica::where('activa', true)->update(['activa' => false]);
            VacacionPolitica::create($validated);
        });

        $this->auditoria("Política de vacaciones actualizada: {$validated['dias_por_anio']} días/año, vigente desde {$validated['vigente_desde']}");

        return redirect()->route('sgrh.vacacion.politica.index')->with('success', 'Política de vacaciones actualizada correctamente.');
    }
}
