<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Permiso para el indicador de uso de la aplicación (área/usuario más activo, tiempo activo).
 * Separado de 'admin.auditoria.index' porque combina datos de varios módulos (incluido
 * Certificados, que audita aparte) para un reporte gerencial, no para revisar acciones puntuales.
 */
class InformeUsoPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'admin.informeuso.index', 'guard_name' => 'web']);
    }
}
