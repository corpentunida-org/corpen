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
        Schema::create('MAEC_EXSER', function (Blueprint $table) {
            $table->id();
            $table->date('fechaRegistro');
            $table->time('horaFallecimiento');
            $table->bigInteger('cedulaTitular');
            $table->string("nombreTitular");
            $table->bigInteger('cedulaFallecido');
            $table->string("nombreFallecido");
            $table->date('fechaFallecimiento');
            $table->string('lugarFallecimiento');
            $table->string('parentesco');
            $table->boolean('traslado')->default(true);
            $table->string('contacto');
            $table->string('telefonoContacto');
            $table->string('Contacto2')->nullable();
            $table->string('telefonoContacto2')->nullable();
            $table->string('factura')->nullable();
            $table->string('valor')->nullable();
            $table->boolean('estado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
