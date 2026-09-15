<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Igual que la importación de Congregaciones y Pastores: escribe en lote sobre MaeTerceros, se
 * gatea con permiso propio en vez del simple 'auth' que usa el resto de Maestras.
 */
class ComaeTerImportarPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'maestras.comaeter.importar', 'guard_name' => 'web']);
    }
}
