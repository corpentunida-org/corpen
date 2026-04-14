<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('con_cuentas_bancarias', function (Blueprint $table) {
            $table->id(); 
            $table->text('banco');
            $table->integer('numero_cuenta');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('con_cuentas_bancarias');
    }
};