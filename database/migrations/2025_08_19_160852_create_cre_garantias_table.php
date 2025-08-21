<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cre_garantias', function (Blueprint $table) {
            // PK: id: AI (Auto-Increment)
            $table->id();

            // Atributo: nombre: STRING
            // Se añade unique() porque los nombres de las garantías no deberían repetirse.
            $table->string('nombre')->unique();

            // Timestamps (created_at y updated_at) para auditoría.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cre_garantias');
    }
};