<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntAlertaConfig;
use App\Models\Soportes\ScpAlertaConfig;
use Illuminate\Http\Request;

/**
 * Una sola pantalla ("Configuración de Alertas") con dos secciones — Interacciones (Daytrack) y
 * Soportes — cada una con su propio formulario/endpoint de guardado, mismo permiso para las dos
 * (admin.alertas_interacciones.config; no se creó un permiso aparte para Soportes, son la misma
 * pantalla de administración global).
 */
class AlertasInteraccionesConfigController extends Controller
{
    public function edit()
    {
        $config = IntAlertaConfig::actual();
        $configSoportes = ScpAlertaConfig::actual();

        return view('admin.interacciones.alertas-config', compact('config', 'configSoportes'));
    }

    public function update(Request $request)
    {
        $validado = $request->validate([
            'aviso_intervalo_horas' => 'required|integer|min:1|max:24',
            'dias_posponer_para_escalar' => 'required|integer|min:1|max:30',
            'correo_diario_hora' => 'required|date_format:H:i',
            'informe_semanal_dia' => 'required|integer|min:1|max:7',
            'informe_semanal_hora' => 'required|date_format:H:i',
            'inactividad_hora' => 'required|date_format:H:i',
        ]);

        // Fila única (singleton) — si por algún motivo no existe todavía, se crea aquí mismo.
        $config = IntAlertaConfig::first() ?? new IntAlertaConfig();
        $config->fill($validado);
        $config->updated_by = auth()->id();
        $config->save();

        IntAlertaConfig::olvidarCache();

        return redirect()->route('admin.alertas-interacciones.config.edit')
            ->with('success', 'Configuración de alertas de Interacciones guardada correctamente.');
    }

    public function updateSoportes(Request $request)
    {
        $validado = $request->validate([
            'aviso_intervalo_horas' => 'required|integer|min:1|max:24',
            'dias_posponer_para_escalar' => 'required|integer|min:1|max:30',
        ]);

        $config = ScpAlertaConfig::first() ?? new ScpAlertaConfig();
        $config->fill($validado);
        $config->updated_by = auth()->id();
        $config->save();

        ScpAlertaConfig::olvidarCache();

        return redirect()->route('admin.alertas-interacciones.config.edit')
            ->with('success', 'Configuración de alertas de Soportes guardada correctamente.');
    }
}
