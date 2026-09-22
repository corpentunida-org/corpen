<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_sia_operaciones', function (Blueprint $table) {
            // Null = Automático - 1 = "Manual" (según tu imagen)
            $table->unsignedTinyInteger('metodo_creacion')->nullable()->after('id_tercero');
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones', function (Blueprint $table) {
            $table->dropColumn('metodo_creacion');
        });
    }
};
