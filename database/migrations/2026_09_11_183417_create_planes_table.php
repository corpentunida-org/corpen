<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catálogo local de planes de Exequiales. Antes solo vivía hardcodeado en un switch dentro de
// PlanController::nomCodPlan() y se leía en vivo de SiaSoft en PlanController::index() — se
// centraliza aquí para no depender de SiaSoft para mostrar el nombre de un plan ya asignado.
// `code` se guarda con cero a la izquierda (mismo formato que showpdf/showpdf2 y SiaSoft usan
// para codePlan), aunque EXE_ExCli.cod_plan tiene formatos mixtos ('1', '01', 'NULL' literal).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
        });

        DB::table('planes')->insert([
            ['code' => '01', 'name' => 'Plan Basico'],
            ['code' => '02', 'name' => 'Plan Ejecutivo'],
            ['code' => '03', 'name' => 'Plan Unipersonal'],
            ['code' => '04', 'name' => 'Plan Exento Pago'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
};
