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
        $seguros = Role::create(['name'=>'seguros']);
        $viewer = Role::create(['name'=>'read']);
    }
}
