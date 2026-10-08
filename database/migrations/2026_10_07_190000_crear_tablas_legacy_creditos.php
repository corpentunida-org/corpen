<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Archivo histórico del sistema de créditos anterior (hojas Comae_soli, Comov_gast, Comov_ing,
 * copagare, ~85.000 filas de 2016 a hoy). Se preservan en tablas propias, separadas de
 * cre_creditos/cre_lineas_creditos (el esquema nuevo ya limpio), porque:
 *  - tip_cred aquí usa códigos "01"-"14" que no corresponden a los `cuenta` de
 *    cre_lineas_creditos (esquema de 8 dígitos tipo 13701015) — son catálogos distintos y no
 *    hay tabla de equivalencia.
 *  - Comae_soli tiene 58 columnas de flujo de aprobación (recibido/aprobado/aplazado/
 *    negado/pagaré enviado/lista desembolso/desembolsado, cada una con fecha y observación)
 *    que no caben en el cre_creditos actual sin perder detalle histórico.
 * Son tablas de solo archivo/consulta, no participan en el flujo activo de creditos.*.
 *
 * doc_deu/cod_deu no llevan FK forzada hacia MaeTerceros: el 99.97% de las cédulas sí existen,
 * pero forzar la constraint rompería el import por ese puñado de filas huérfanas (datos de
 * clientes históricos removidos de la maestra). Igual con num_soli/n_soli entre tablas: 1-5% de
 * movimientos/pagarés no tienen solicitud madre en el archivo original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cre_legacy_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('idrow')->nullable();
            $table->string('num_soli', 20)->unique();
            $table->date('fecha')->nullable();
            $table->unsignedBigInteger('doc_deu')->nullable()->index();
            $table->string('tipo_cred', 10)->nullable();
            $table->string('tipo_cuota', 10)->nullable();
            $table->string('cod_congre', 20)->nullable();
            $table->decimal('vr_soli', 15, 2)->nullable();
            $table->integer('plazo')->nullable();
            $table->text('destino')->nullable();
            $table->string('estado', 10)->nullable();
            $table->decimal('val_aprobado', 15, 2)->nullable();
            $table->decimal('ingresos', 15, 2)->nullable();
            $table->decimal('egresos', 15, 2)->nullable();
            $table->text('observacion')->nullable();
            $table->decimal('por_inte', 8, 5)->nullable();
            $table->decimal('por_seguro', 8, 5)->nullable();
            $table->integer('antigueda')->nullable(); // así, sin "d" final, tal cual el archivo original
            $table->decimal('val_cuota', 15, 2)->nullable();
            $table->decimal('mont_maximo', 15, 2)->nullable();
            $table->decimal('mont_posible', 15, 2)->nullable();
            $table->string('estsoli', 10)->nullable();
            $table->string('data_cre', 10)->nullable();
            $table->integer('puntaje_datacre')->nullable();
            $table->string('otras_areas', 10)->nullable();
            $table->string('creditos', 10)->nullable();
            $table->string('documentos', 10)->nullable();
            $table->integer('antiguedad')->nullable(); // columna distinta de "antigueda" arriba, ambas existían en el archivo
            $table->string('rut', 10)->nullable();
            $table->decimal('visto_directivos', 15, 2)->nullable();
            $table->integer('total_cuotas')->nullable();
            $table->boolean('est_recibi')->nullable();
            $table->boolean('est_aprob')->nullable();
            $table->boolean('est_aplaz')->nullable();
            $table->boolean('est_nega')->nullable();
            $table->boolean('est_pagenvia')->nullable();
            $table->boolean('est_lisdesem')->nullable();
            $table->boolean('est_desem')->nullable();
            $table->date('fec_recibi')->nullable();
            $table->date('fec_aprob')->nullable();
            $table->date('fec_aplaz')->nullable();
            $table->date('fec_nega')->nullable();
            $table->date('fec_pagenvia')->nullable();
            $table->date('fec_lisdesem')->nullable();
            $table->date('fec_desem')->nullable();
            $table->text('obs_recibi')->nullable();
            $table->text('obs_aprob')->nullable();
            $table->text('obs_aplaz')->nullable();
            $table->text('obs_nega')->nullable();
            $table->text('obs_pagenvia')->nullable();
            $table->text('obs_lisdesem')->nullable();
            $table->text('obs_desem')->nullable();
            $table->string('cod_dist', 20)->nullable();
            $table->string('protec_dato', 10)->nullable();
            $table->string('tipo_vivi', 30)->nullable();
            $table->string('tiposoli', 30)->nullable();
            $table->decimal('por_segtodoriesgo', 8, 5)->nullable();
            $table->decimal('val_segtodoriesgo', 15, 2)->nullable();
        });

        Schema::create('cre_legacy_movimientos_gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('idrow')->nullable();
            $table->string('num_soli', 20)->index();
            $table->string('cod_egre', 10)->nullable();
            $table->decimal('valor_egre', 15, 2)->nullable();
        });

        Schema::create('cre_legacy_movimientos_ingresos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('idrow')->nullable();
            $table->string('num_soli', 20)->index();
            $table->string('cod_ing', 10)->nullable();
            $table->decimal('valor_ing', 15, 2)->nullable();
        });

        Schema::create('cre_legacy_pagares', function (Blueprint $table) {
            $table->id();
            $table->string('cod_pagare', 20)->nullable();
            $table->string('n_soli', 20)->index();
            $table->unsignedBigInteger('cod_deu')->nullable()->index();
            $table->decimal('mont_aprob', 15, 2)->nullable();
            $table->decimal('cuota', 15, 2)->nullable();
            $table->integer('plazo')->nullable();
            $table->date('fec_apro')->nullable();
            $table->date('fec_inicia')->nullable();
            $table->string('nomusu', 100)->nullable();
            $table->decimal('intere', 8, 5)->nullable();
            $table->string('estado', 10)->nullable();
            $table->string('tip_cred', 10)->nullable();
            $table->decimal('por_seguro', 8, 5)->nullable();
            $table->string('tipo_cuota', 10)->nullable();
            $table->decimal('por_segtodoriesgo', 8, 5)->nullable();
            $table->decimal('val_segtodoriesgo', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cre_legacy_pagares');
        Schema::dropIfExists('cre_legacy_movimientos_ingresos');
        Schema::dropIfExists('cre_legacy_movimientos_gastos');
        Schema::dropIfExists('cre_legacy_solicitudes');
    }
};
