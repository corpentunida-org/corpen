<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * La ruta de "terceros" en Exequiales (exequial.terceros.*) solo tenía middleware 'auth', sin
 * 'can:', porque estos permisos nunca se habían creado — a diferencia de asociados,
 * beneficiarios y prestarServicio, que sí los tienen. Idempotente (firstOrCreate).
 */
class ExequialTercerosPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'exequial.terceros.index',
            'exequial.terceros.store',
            'exequial.terceros.update',
            'exequial.terceros.destroy',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }
    }
}
