<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScpSoportesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scp_soportes', function (Blueprint $table) {
            $table->id();
            $table->string('detalles_soporte');
            $table->string('timestam'); // Se mantiene como string según el diagrama
            // Claves foráneas:
            $table->foreignId('id_gdo_cargo')->constrained('gdo_cargo')->cascadeOnDelete(); // Asumiendo plural 'gdo_cargos'
            $table->foreignId('id_cre_lineas_creditos')->constrained('cre_lineas_creditos')->cascadeOnDelete(); // Asumiendo el mismo nombre para la tabla referenciada
            $table->bigInteger('cod_ter_maeTercero');$table->foreign('cod_ter_maeTercero')->references('cod_ter')->on('maeTerceros')->onDelete('cascade');
            $table->foreignId('id_scp_tipo')->constrained('scp_tipos')->cascadeOnDelete(); // Usando 'scp_tipos'
            $table->foreignId('id_scp_prioridad')->constrained('scp_prioridads')->cascadeOnDelete(); // Usando 'scp_prioridads'
            $table->foreignId('id_users')->comment('quien crea incidencia')->constrained('users')->cascadeOnDelete(); // Asumiendo 'users'
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
        Schema::dropIfExists('scp_soportes');
    }
}