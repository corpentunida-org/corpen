<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// El PDF firmado del contrato ya no se sube/almacena en S3 desde SGRH: la empresa ya tiene
// un gestor documental aparte donde viven esos archivos. documento_path (ruta S3) se
// reemplaza por documento_url (enlace externo al gestor documental) — el único valor que
// tenía documento_path era un residuo de pruebas, sin dato real que preservar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->dropColumn('documento_path');
            $table->string('documento_url', 2048)->nullable()->after('salario_contrato');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_contratos', function (Blueprint $table) {
            $table->dropColumn('documento_url');
            $table->string('documento_path')->nullable()->after('salario_contrato');
        });
    }
};
