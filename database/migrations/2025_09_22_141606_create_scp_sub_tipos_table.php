<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scp_sub_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scp_tipo_id')->constrained('scp_tipos')->onDelete('cascade');
            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('scp_sub_tipos');
    }
};

