<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * pr y pagare se crearon como int, pero en la práctica son códigos alfanuméricos
 * (ej: "PG-00123"), no números puros. Se amplían a varchar; los 2 registros existentes
 * tienen valores puramente numéricos, así que la conversión no pierde datos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cre_creditos', function (Blueprint $table) {
            $table->string('pr', 255)->change();
            $table->string('pagare', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('cre_creditos', function (Blueprint $table) {
            $table->integer('pr')->change();
            $table->integer('pagare')->change();
        });
    }
};
