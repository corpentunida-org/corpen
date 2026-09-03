<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\Dependiente;
use App\Models\Sgrh\Empleado;
use Illuminate\Http\Request;

class DependienteController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function store(Request $request, Empleado $empleado)
    {
        $validated = $this->validado($request);
        $validated['empleado_id'] = $empleado->id;

        $dependiente = Dependiente::create($validated);

        $this->auditoria("Dependiente económico agregado a colaborador #{$empleado->id}: {$dependiente->nombre_completo}");

        return redirect()->route('sgrh.empleado.edit', $empleado)->with('success', 'Dependiente agregado correctamente.');
    }

    public function update(Request $request, Dependiente $dependiente)
    {
        $validated = $this->validado($request);

        $dependiente->update($validated);

        $this->auditoria("Dependiente económico #{$dependiente->id} actualizado (colaborador #{$dependiente->empleado_id})");

        return redirect()->route('sgrh.empleado.edit', $dependiente->empleado_id)->with('success', 'Dependiente actualizado correctamente.');
    }

    public function destroy(Dependiente $dependiente)
    {
        $empleadoId = $dependiente->empleado_id;
        $nombre = $dependiente->nombre_completo;
        $dependiente->delete();

        $this->auditoria("Dependiente económico eliminado (colaborador #{$empleadoId}): {$nombre}");

        return redirect()->route('sgrh.empleado.edit', $empleadoId)->with('success', 'Dependiente eliminado correctamente.');
    }

    private function validado(Request $request): array
    {
        $validated = $request->validate([
            'nombre1' => 'required|string|max:255',
            'nombre2' => 'nullable|string|max:255',
            'apellido1' => 'required|string|max:255',
            'apellido2' => 'nullable|string|max:255',
            'tipo_documento' => 'nullable|string|exists:tipo_documentos,codigo',
            'documento_identificacion' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'nullable|in:V,H',
            'parentesco' => 'nullable|string|max:10',
        ]);

        // Nombres y apellidos siempre en mayúsculas y sin espacios de sobra (al inicio/final ni
        // dobles entre palabras) — mismo criterio que MaeTerceros y el resto de campos de
        // nombre en SGRH.
        foreach (['nombre1', 'nombre2', 'apellido1', 'apellido2'] as $campo) {
            if (!empty($validated[$campo])) {
                $validated[$campo] = mb_strtoupper(trim(preg_replace('/\s+/', ' ', $validated[$campo])), 'UTF-8');
            }
        }

        if (!empty($validated['documento_identificacion'])) {
            $validated['documento_identificacion'] = trim($validated['documento_identificacion']);
        }

        return $validated;
    }
}
