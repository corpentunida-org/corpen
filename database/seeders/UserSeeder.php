<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*if (!User::where('email', 'superadmin@corpentunida.org.co')->exists()) {

        }
        if (!User::where('email', 'exequiales@corpentunida.org.co')->exists()) {

        }
        if (!User::where('email', 'administracion@corpentunida.org.co')->exists()) {

        }
        if (!User::where('email', 'superadmin@corpentunida.org.co')->exists()) {
            
        } */
        User::create([
            'name' => 'admin',
            'email' => 'superadmin@corpentunida.org.co',
            'password' => bcrypt('Hola12345')
        ])->assignRole('admin');

        User::create([
            'name' => 'Daniel Casallas',
            'email' => 'exequiales@corpentunida.org.co',
            'password' => bcrypt('Dd123456')
        ])->assignRole('exequial');

        User::create([
            'name' => 'Carlos Vasquez',
            'email' => 'administracion@corpentunida.org.co',
            'password' => bcrypt('Cc123456')
        ])->assignRole('read');

        User::create([
            'name' => 'Johanna Rivera',
            'email' => 'seguros@corpentunida.org.co',
            'password' => bcrypt('Jj123456')
        ])->assignRole('seguros');
        //User::factory(5)->create();
    }
}