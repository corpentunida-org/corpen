<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_sia_tipos', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, PK, NOT NULL, AUTO_INCREMENT
            $table->string('nombre', 100); // VARCHAR(100), NOT NULL
            $table->boolean('estado')->default(true); // BOOLEAN, NOT NULL, DEFAULT TRUE
            $table->string('estructura_radicado', 50); // VARCHAR(50), NOT NULL
            $table->timestamps(); // created_at y updated_at (TIMESTAMP NULL)
            $table->softDeletes(); // deleted_at (TIMESTAMP NULL)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_sia_tipos');
    }
};
