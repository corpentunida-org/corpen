<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// fecha_inicio deja de ser obligatoria para contratos a término Indefinido — puede no
// conocerse de entrada. Sigue siendo obligatoria a nivel de aplicación para el resto de tipos
// (ver ContratoController::validado()).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable(false)->change();
        });
    }
};
