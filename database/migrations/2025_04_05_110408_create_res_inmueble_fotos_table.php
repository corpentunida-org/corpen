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
        Schema::create('res_inmueble_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('res_inmueble_id')->constrained('res_inmuebles')->onDelete('cascade');
            $table->string('attached');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('res_inmueble_fotos');
    }
};
