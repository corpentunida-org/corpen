<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Permiso para administrar credenciales de integraciones externas sensibles
 * (hoy: CRM Corpentunida). Deliberadamente separado de 'admin.auditoria.index'
 * (el permiso que hoy gatea todo el menú "Integraciones") porque este permite
 * ver/cambiar un client_secret, no solo consultar diagnósticos.
 */
class AdminIntegracionesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'admin.integraciones.corpentunida.index', 'guard_name' => 'web']);
    }
}
