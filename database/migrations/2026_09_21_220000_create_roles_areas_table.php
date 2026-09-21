<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Áreas de la Matriz de Permisos como entidad propia: así existen aunque aún no tengan perfiles
 * y se pueden crear/renombrar/eliminar. roles.area guarda el nombre del área de cada perfil.
 * Aditiva: el código anterior no la usa.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles_areas')) {
            Schema::create('roles_areas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 60)->unique();
                $table->timestamps();
            });
        }

        $existentes = DB::table('roles')->get(['name', 'area'])
            ->map(fn ($r) => mb_strtolower($r->area ?: $r->name))->unique();
        foreach ($existentes as $nombre) {
            DB::table('roles_areas')->insertOrIgnore(['nombre' => $nombre, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('roles_areas');
    }
};
