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
        Schema::create('rsv_inmueble_multimedia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_catalogo_inmueble')->constrained('rsv_catalogo_inmueble')->cascadeOnDelete();
            $table->string('url_archivo', 255);
            $table->string('tipo_multimedia', 50);
            $table->integer('orden');
            $table->boolean('es_portada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_inmueble_multimedia');
    }
};
