<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scp_usuarios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cod_ter'); // 👈 debe coincidir EXACTAMENTE con MaeTerceros.cod_ter
            $table->string('rol')->nullable();
            $table->timestamps();

            $table->foreign('cod_ter')
                  ->references('cod_ter')
                  ->on('MaeTerceros')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scp_usuarios');
    }
};
