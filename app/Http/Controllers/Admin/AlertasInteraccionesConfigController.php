<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntAlertaConfig;
use App\Models\Interacciones\IntAlertaOmitido;
use App\Models\Soportes\ScpAlertaConfig;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Configuración de Alertas — Interacciones (Daytrack) y Soportes tienen cada una su PROPIA
 * pantalla (mismo permiso para las dos: admin.alertas_interacciones.config; no se creó uno
 * aparte para Soportes, son la misma administración global), enlazadas por separado desde el
 * menú Admin. Los "Agentes Omitidos" (correo/pantalla) son una lista compartida entre los dos
 * módulos (ver IntAlertaOmitido) — vive en la pantalla de Interacciones para no duplicar el
 * punto de edición; la de Soportes solo enlaza hacia allá.
 */
class AlertasInteraccionesConfigController extends Controller
{
    public function edit()
    {
        $config = IntAlertaConfig::actual();

        $omitidos = IntAlertaOmitido::with(['usuario:id,name,email', 'creadoPor:id,name'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('tipo');

        // Cualquiera con acceso a Interacciones o a Soportes es candidato a omitir — la misma
        // lista sirve para los dos selects (correo/pantalla).
        $candidatos = User::whereNull('deleted_at')
            ->where(fn ($q) => $q->where('bloqueado', false)->orWhereNull('bloqueado'))
            ->where(fn ($q) => $q->whereHas('permissions', fn ($q2) => $q2->where('name', 'menu.interacciones'))
                ->orWhereHas('roles', fn ($q2) => $q2->where('name', 'soporte')))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.interacciones.alertas-config', compact('config', 'omitidos', 'candidatos'));
    }

    public function editSoportes()
    {
        $configSoportes = ScpAlertaConfig::actual();

        return view('admin.soportes.alertas-config', compact('configSoportes'));
    }

    public function update(Request $request)
    {
        $validado = $request->validate([
            'aviso_intervalo_horas' => 'required|integer|min:1|max:24',
            'recordatorio_matutino_hora' => 'required|date_format:H:i',
            'pulso_intervalo_minutos' => 'required|integer|min:1|max:120',
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
            'pulso_intervalo_minutos' => 'required|integer|min:1|max:120',
            'dias_posponer_para_escalar' => 'required|integer|min:1|max:30',
            'dias_cierre_automatico' => 'required|integer|min:1|max:60',
        ]);

        $config = ScpAlertaConfig::first() ?? new ScpAlertaConfig();
        $config->fill($validado);
        $config->updated_by = auth()->id();
        $config->save();

        ScpAlertaConfig::olvidarCache();

        return redirect()->route('admin.alertas-soportes.config.edit')
            ->with('success', 'Configuración de alertas de Soportes guardada correctamente.');
    }

    /** Agrega un agente a la lista de omitidos (correo o pantalla). */
    public function agregarOmitido(Request $request)
    {
        $validado = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'tipo' => 'required|in:correo,pantalla,informe',
        ]);

        // firstOrCreate respeta el UNIQUE(user_id, tipo) — si ya estaba omitido no duplica.
        IntAlertaOmitido::firstOrCreate(
            ['user_id' => $validado['user_id'], 'tipo' => $validado['tipo']],
            ['created_by' => auth()->id()]
        );

        IntAlertaOmitido::olvidarCache();

        return redirect()->route('admin.alertas-interacciones.config.edit')
            ->with('success', 'Agente agregado a la lista de omitidos.');
    }

    /** Quita a un agente de la lista de omitidos ("volver a habilitar"). */
    public function quitarOmitido(IntAlertaOmitido $omitido)
    {
        $omitido->delete();

        IntAlertaOmitido::olvidarCache();

        return redirect()->route('admin.alertas-interacciones.config.edit')
            ->with('success', 'Agente reactivado — vuelve a recibir esa alerta.');
    }
}
