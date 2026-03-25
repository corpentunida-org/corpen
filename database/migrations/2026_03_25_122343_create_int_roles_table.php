<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('int_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique(); 
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insertamos los roles por defecto inmediatamente
        DB::table('int_roles')->insert([
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Control total del chat'],
            ['name' => 'Empleado', 'slug' => 'employee', 'description' => 'Usuario interno de la empresa'],
            ['name' => 'Cliente', 'slug' => 'client', 'description' => 'Usuario externo'],
            ['name' => 'Auditor', 'slug' => 'auditor', 'description' => 'Solo lectura para revisión'],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('int_roles');
    }
};
