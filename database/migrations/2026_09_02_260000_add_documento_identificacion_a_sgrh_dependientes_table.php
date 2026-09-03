<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Documento de identificación del dependiente (registro civil/tarjeta de identidad/cédula
// según la edad) — opcional, no todos los dependientes recién nacidos lo tienen asignado aún.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_dependientes', function (Blueprint $table) {
            $table->string('documento_identificacion', 20)->nullable()->after('apellido2');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_dependientes', function (Blueprint $table) {
            $table->dropColumn('documento_identificacion');
        });
    }
};
