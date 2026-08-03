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
        Schema::create('rsv_mensajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rsv_reservas')->constrained('rsv_reservas')->cascadeOnDelete();
            $table->foreignId('id_user_remitente')->constrained('users')->cascadeOnDelete();

            $table->string('tipo_mensaje', 50);
            $table->text('contenido');
            $table->string('url_archivo', 255)->nullable();
            $table->timestamp('leido_en')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsv_mensajes');
    }
};
