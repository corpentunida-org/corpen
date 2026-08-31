<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Acelera la búsqueda de terceros por nombre en SGRH (EmpleadoController::buscarTercero()).
 * Medido antes de aplicar: LIKE '%texto%' sobre nom1/nom2/apl1/apl2/nom_ter tardaba
 * ~670-700ms (no puede usar índice normal por el comodín al inicio); con FULLTEXT +
 * MATCH/AGAINST bajó a ~150-160ms (~4.3x). No afecta a otros módulos: es aditivo, nadie más
 * usa MATCH/AGAINST sobre esta tabla, sus consultas LIKE existentes siguen funcionando igual.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE MaeTerceros ADD FULLTEXT INDEX ft_mae_terceros_nombres (nom1, nom2, apl1, apl2, nom_ter)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE MaeTerceros DROP INDEX ft_mae_terceros_nombres');
    }
};
