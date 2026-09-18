<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índice normal (no único todavía) sobre 'nid' — hoy no tiene ningún índice, así que cada
     * búsqueda/validación por cédula hace un escaneo completo de la tabla. No se crea como
     * UNIQUE en este paso porque ya existen 73 cédulas con más de una cuenta (155 filas en
     * total); un índice único fallaría directo contra esos datos. Convertirlo a único es un
     * paso aparte, después de resolver esos duplicados.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('nid');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['nid']);
        });
    }
};
