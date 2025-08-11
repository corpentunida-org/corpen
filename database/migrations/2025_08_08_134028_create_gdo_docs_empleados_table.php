<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gdo_docs_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('ruta_archivo', 255); // Ruta o nombre del archivo
            $table->date('fecha_subida'); // Fecha de subida
            $table->text('observaciones')->nullable(); // Observaciones opcionales
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gdo_docs_empleados');
    }
};
