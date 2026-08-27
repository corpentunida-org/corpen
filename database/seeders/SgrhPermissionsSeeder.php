<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

/**
 * Ya se crearon manualmente en producción (vía tinker) durante el desarrollo de este bloque,
 * pero no existía ningún archivo que los reprodujera — este seeder documenta esa acción en
 * código, para que un entorno nuevo (staging, disaster recovery, otro desarrollador) quede
 * igual sin depender de recordar el comando de tinker. Es idempotente: correrlo de nuevo no
 * duplica ni pisa nada.
 */
class SgrhPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'sgrh.tercero.show', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'sgrh.tercero.edit', 'guard_name' => 'web']);
    }
}
