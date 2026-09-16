<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            // Agrega el campo al final (después de metadata)
            $table->string('estadoApi', 50)
                  ->nullable()
                  ->after('metadata');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            $table->dropColumn('estadoApi');
        });
    }
};
