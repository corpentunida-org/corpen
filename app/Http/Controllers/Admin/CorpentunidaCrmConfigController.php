<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCorpentunidaCrmConfigRequest;
use App\Models\Integraciones\CorpentunidaCrmConfig;
use Illuminate\Support\Facades\Cache;

class CorpentunidaCrmConfigController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'ADMINISTRACIÓN');
    }

    public function edit()
    {
        $config = CorpentunidaCrmConfig::actual();
        return view('admin.integraciones.corpentunida-crm', compact('config'));
    }

    public function update(UpdateCorpentunidaCrmConfigRequest $request)
    {
        $config = CorpentunidaCrmConfig::actual() ?? new CorpentunidaCrmConfig();

        $config->url = $request->url;
        $config->client_id = $request->client_id;
        // Solo se sobrescribe el secreto si mandaron uno nuevo — el campo llega
        // vacío en el form cuando el admin no quiso cambiarlo (nunca se
        // pre-rellena con el valor real, así que no hay "vacío accidental").
        if ($request->filled('client_secret')) {
            $config->client_secret = $request->client_secret;
        }
        $config->updated_by = auth()->id();
        $config->save();

        // El token cacheado (si existía) quedó de credenciales viejas.
        Cache::forget('corpentunida_crm.token');

        $this->auditoria('actualizó la configuración de la API Corpentunida CRM');

        return redirect()->route('integraciones.corpentunida-crm.edit')
            ->with('success', 'Configuración del CRM Corpentunida guardada correctamente.');
    }
}
