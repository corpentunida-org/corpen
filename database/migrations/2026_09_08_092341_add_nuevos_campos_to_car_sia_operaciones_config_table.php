<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_operaciones_config', function (Blueprint $table) {
            // Se define nullable() para no romper los registros que ya existen en la BD.
            // Asumimos que hace referencia a la tabla 'users' estándar de Laravel.
            $table->foreignId('id_user')
                  ->nullable()
                  ->after('estado_notificacion')
                  ->constrained('users');

            $table->text('justificacion')
                  ->nullable()
                  ->after('id_user');

            $table->timestamp('vigente_hasta')
                  ->nullable()
                  ->after('justificacion');

            $table->boolean('estado_activo')
                  ->default(true)
                  ->after('vigente_hasta');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones_config', function (Blueprint $table) {
            // Primero se debe eliminar la llave foránea antes de la columna
            $table->dropForeign(['id_user']);

            $table->dropColumn([
                'id_user',
                'justificacion',
                'vigente_hasta',
                'estado_activo'
            ]);
        });
    }
};
