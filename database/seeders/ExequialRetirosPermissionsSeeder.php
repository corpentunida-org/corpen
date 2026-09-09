<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Permisos del control de retiros de titulares (Exequiales). Mismo patrón que
 * ExequialTercerosPermissionsSeeder: idempotente (firstOrCreate).
 */
class ExequialRetirosPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'exequial.retiros.store',
            'exequial.retiros.index',
            'exequial.retiros.reportar',
            'exequial.retiros.reafiliar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }
    }
}
