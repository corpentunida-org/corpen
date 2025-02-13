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
        Schema::create('SEG_reclamaciones', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cedulaAsegurado');
            $table->bigInteger('idCobertura');
            $table->bigInteger('idDiagnostico');
            $table->string('otro');
            $table->date('fechaSinistro');

            #primer contacto
            $table->date('fechaContacto');
            $table->time('horaContacto');
            $table->string('nombreContacto', length:100);
            $table->string('parentescoContacto');
            $table->timestamps();

            #estado
            $table->Integer('estado');
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
