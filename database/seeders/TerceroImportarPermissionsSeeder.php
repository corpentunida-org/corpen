<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Igual que la importación de Congregaciones: escribe en lote sobre MaeTerceros, se gatea con
 * permiso propio en vez del simple 'auth' que usa el resto de Maestras.
 */
class TerceroImportarPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'maestras.terceros.importar', 'guard_name' => 'web']);
    }
}
