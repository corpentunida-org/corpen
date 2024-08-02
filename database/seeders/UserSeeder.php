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
        User::create([
            'name' => 'admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('Hola12345')
        ])->assignRole('admin');

        //User::factory(5)->create();
    }
}