<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Empleado;
use App\Models\Sgrh\Estudio;
use Illuminate\Http\Request;

class EstudioController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    // Catálogo fijo de nivel educativo (mismo listado que ya usa la entidad para PILA/RUAF) —
    // no se modela como tabla aparte porque es una lista cerrada que casi no cambia, igual
    // criterio que ContratoController::CAUSALES_MODIFICACION.
    public const NIVELES_FORMACION = [
        'Educación básica primaria',
        'Educación básica secundaria',
        'Bachiller',
        'Normalista',
        'Técnico profesional',
        'Especialización técnica profesional',
        'Tecnológico',
        'Especialización tecnológica',
        'Profesional',
        'Especialización profesional',
        'Maestría',
        'Doctorado',
        'Postdoctorado',
        'Formación laboral',
        'Formación penitenciaria',
        'Educación informal',
    ];

    public const TIPOS_FORMACION = ['Formal', 'Informal'];

    public function store(Request $request, Empleado $empleado)
    {
        $validated = $this->validado($request);
        $validated['empleado_id'] = $empleado->id;

        $estudio = Estudio::create($validated);

        $this->auditoria("Estudio agregado al colaborador #{$empleado->id}: {$estudio->programa}");

        return redirect()->route('sgrh.empleado.edit', $empleado)->with('success', 'Estudio agregado correctamente.');
    }

    public function update(Request $request, Estudio $estudio)
    {
        $validated = $this->validado($request);

        $estudio->update($validated);

        $this->auditoria("Estudio #{$estudio->id} actualizado (colaborador #{$estudio->empleado_id})");

        return redirect()->route('sgrh.empleado.edit', $estudio->empleado_id)->with('success', 'Estudio actualizado correctamente.');
    }

    public function destroy(Estudio $estudio)
    {
        $empleadoId = $estudio->empleado_id;
        $programa = $estudio->programa;
        $estudio->delete();

        $this->auditoria("Estudio eliminado (colaborador #{$empleadoId}): {$programa}");

        return redirect()->route('sgrh.empleado.edit', $empleadoId)->with('success', 'Estudio eliminado correctamente.');
    }

    private function validado(Request $request): array
    {
        $validated = $request->validate([
            'programa' => 'required|string|max:255',
            'institucion_educativa' => 'nullable|string|max:255',
            'tipo_formacion' => 'required|in:' . implode(',', self::TIPOS_FORMACION),
            'nivel_formacion' => 'required|in:' . implode(',', self::NIVELES_FORMACION),
            'graduado' => 'nullable|boolean',
            'fecha_terminacion' => 'nullable|date',
        ]);

        $validated['graduado'] = $request->boolean('graduado');
        $validated['programa'] = trim(preg_replace('/\s+/', ' ', $validated['programa']));

        if (!empty($validated['institucion_educativa'])) {
            $validated['institucion_educativa'] = trim(preg_replace('/\s+/', ' ', $validated['institucion_educativa']));
        }

        return $validated;
    }
}
