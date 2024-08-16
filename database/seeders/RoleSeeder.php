<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role; //Modelo Rol
use Spatie\Permission\Models\Permission; //Modelo Permisos

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //crear Roles
        $admin = Role::create(['name'=>'admin']);
        $exequial = Role::create(['name'=>'exequial']);
        $creditos = Role::create(['name'=>'creditos']);
        $viewer = Role::create(['name'=>'read']);

        //crear Permisos
        Permission::create(['name'=>'exequial.asociados.index'])->syncRoles([$admin, $exequial, $viewer]); //Asignando el permiso al rol Admin, Exequial, Viewer
        Permission::create(['name'=>'exequial.asociados.show'])->syncRoles([$admin, $exequial, $viewer]);
        Permission::create(['name'=>'exequial.asociados.store'])->syncRoles([$admin, $exequial]);
        Permission::create(['name'=>'exequial.asociados.update'])->syncRoles([$admin, $exequial]);
        Permission::create(['name'=>'exequial.asociados.destroy'])->syncRoles([$admin, $exequial]);

        Permission::create(['name'=>'exequial.beneficiarios.show'])->syncRoles([$admin, $exequial, $viewer]);
        Permission::create(['name'=>'exequial.beneficiarios.store'])->syncRoles([$admin, $exequial]);
        Permission::create(['name'=>'exequial.beneficiarios.update'])->syncRoles([$admin, $exequial]);
        Permission::create(['name'=>'exequial.beneficiarios.destroy'])->syncRoles([$admin, $exequial]);

    }
}
