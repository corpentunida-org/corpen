<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tipo de documento del dependiente — reutiliza el catálogo App\Models\Maestras\TipoDocumento
// (tabla tipo_documentos) ya usado por SGRH para MaeTerceros.tdoc, no se crea uno nuevo.
// Se guarda el 'codigo' (varchar(5): "11" Registro Civil, "12" Tarjeta de Identidad, etc.),
// mismo criterio que 'parentesco' guarda el code de Parentesco.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sgrh_dependientes', function (Blueprint $table) {
            $table->string('tipo_documento', 5)->nullable()->after('apellido2');
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_dependientes', function (Blueprint $table) {
            $table->dropColumn('tipo_documento');
        });
    }
};
