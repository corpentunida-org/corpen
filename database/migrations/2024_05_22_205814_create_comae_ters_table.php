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
        Schema::create('coMaeExter', function (Blueprint $table) {
            $table->id();
            $table->string('cod-ter');
            $table->string('nom_ter');
            $table->boolean('estado')->default(true);
            $table->string('apl1')->nullable();
            $table->string('apl2')->nullable();
            $table->string('nom1')->nullable();
            $table->string('nom2')->nullable();
            $table->string('dir')->nullable();
            $table->string('dir1')->nullable();
            $table->string('dir2')->nullable();
            $table->string('tel1')->nullable();
            $table->string('email')->nullable();
            $table->string('fax1')->nullable();
            $table->string('fec_ing')->nullable();
            $table->string('cuidad')->nullable();
            $table->string('tip_prv')->nullable();
            $table->string('cod_act')->nullable();
            $table->string('cod_cla')->nullable();
            $table->string('int_mora')->nullable();
            $table->string('dia_plaz')->nullable();
            $table->string('por_des')->nullable();
            $table->string('observ')->nullable();
            $table->string('aut_ret')->nullable();
            $table->string('por_ica')->nullable();
            $table->string('repres')->nullable();
            $table->string('cta_ban')->nullable();
            $table->string('clasific')->nullable();
            $table->string('cod_can')->nullable();
            $table->string('cod_ven')->nullable();
            $table->string('por_com')->nullable();
            $table->string('ind_suc')->nullable();
            $table->string('ind_cred')->nullable();
            $table->string('cupo_cred')->nullable();
            $table->string('ind_rete')->nullable();
            $table->string('raz')->nullable();
            $table->integer('dpto')->nullable();
            $table->integer('mun')->nullable();
            $table->string('tip_pers')->nullable();
            $table->string('dv')->nullable();
            $table->string('tdoc')->nullable();
            $table->integer('cod_ciu')->nullable();
            $table->string('depa')->nullable();
            $table->string('cargo')->nullable();
            $table->string('cel')->nullable();
            $table->string('pais')->nullable();
            $table->integer('id_ter')->nullable();
            $table->string('cod_ban')->nullable();
            $table->date('fec_minis')->nullable();
            $table->string('cod_dist')->nullable();
            $table->string('cod_est')->nullable();
            $table->string('nom_conyugue')->nullable();
            $table->string('est_civil')->nullable();
            $table->date('fec_falle')->nullable();
            $table->string('sexo')->nullable();
            $table->string('lugar_naci')->nullable();
            $table->string('congrega')->nullable();
            $table->date('fec_aport')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coMaeExter');
    }
};
