<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('int_workspaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            // Relación con gdo_area (Asegúrate que gdo_area use BigInt en su ID)
            $table->foreignId('area_id')->constrained('gdo_area');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('int_workspaces');
    }
};