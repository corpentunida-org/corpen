<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScpObservacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scp_observaciones', function (Blueprint $table) {
            $table->id();
            $table->string('observacion');
            $table->string('timestam'); // Se mantiene como string según el diagrama
            // Claves foráneas:
            $table->foreignId('id_scp_soporte')->constrained('scp_soportes')->cascadeOnDelete(); // Referencia a 'scp_soportes'
            $table->foreignId('id_scp_estados')->constrained('scp_estados')->cascadeOnDelete(); // Referencia a 'scp_estados'
            $table->foreignId('id_users')->comment('quien crea, atiende o redirecciona soporte')->constrained('users')->cascadeOnDelete(); // Asumiendo 'users'
            $table->foreignId('id_tipo_observacion')->constrained('scp_tipo_observacions')->cascadeOnDelete(); // Referencia a 'scp_tipo_observacions'
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
        Schema::dropIfExists('scp_observaciones');
    }
}