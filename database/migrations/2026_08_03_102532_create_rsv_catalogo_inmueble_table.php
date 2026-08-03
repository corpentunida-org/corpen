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
        Schema::create('rsv_catalogo_inmueble', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('city', 255);
            $table->text('ubicacion');
            $table->boolean('active')->default(true);
            $table->integer('capacidad_maxima');
            $table->decimal('precio_base_noche', 10, 2);
            $table->unsignedBigInteger('tipo_inmueble_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_catalogo_inmueble');
    }
};
