<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * attachment_size en bytes, nullable: los adjuntos subidos DESDE esta migración lo guardan de
 * una vez (ver GuardaSoporteInteraccion). Los ya subidos antes quedan en null — Admin → Limpiar
 * Historial de Adjuntos los calcula la primera vez que se navega a ese año (una llamada a S3 por
 * archivo sin tamaño conocido) y los guarda aquí mismo, así la próxima vez ya no hace falta
 * volver a preguntarle a S3.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->unsignedBigInteger('attachment_size')->nullable()->after('attachment_urls');
        });
    }

    public function down(): void
    {
        Schema::table('int_seguimiento', function (Blueprint $table) {
            $table->dropColumn('attachment_size');
        });
    }
};
