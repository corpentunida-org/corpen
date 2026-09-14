<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * A diferencia del resto de Maestras (que solo exige 'auth'), la importación periódica del
 * listado de congregaciones escribe miles de filas de una vez — se gatea con permiso propio.
 */
class CongregacionImportarPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'maestras.congregaciones.importar', 'guard_name' => 'web']);
    }
}
