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
        Schema::create('SEG_beneficiarios', function (Blueprint $table) {
            $table->string('porcentaje');
            $table->boolean('benef_contigente')->default(false);

            $table->bigInteger('cedula');
            $table->foreign('cedula')->references('cedula')->on('SEG_terceros')->onDelete('cascade');

            $table->unsignedBigInteger('idPoliza');
            $table->foreign('idPoliza')->references('id')->on('SEG_polizas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SEG_beneficiarios');
    }   
    //2024_11_18_092800_create_seg_beneficiarios_table
    //2024_11_18_092414_create_seg_polizas_table
};
