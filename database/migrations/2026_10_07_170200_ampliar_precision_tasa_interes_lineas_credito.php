<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * tasa_interes/tasa_interes_alt eran decimal(8,2), que solo guarda 2 decimales. Tasas reales
 * como 0.4% (0.004) o 0.8%/0.9% (0.008/0.009) necesitan 3-4 decimales como fracción y se
 * truncaron a 0.00/0.01 en la migración anterior (2026_10_07_170100). Se amplía a decimal(8,4)
 * y se corrigen los valores ya truncados.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE cre_lineas_creditos MODIFY tasa_interes DECIMAL(8,4) NULL');
        DB::statement('ALTER TABLE cre_lineas_creditos MODIFY tasa_interes_alt DECIMAL(8,4) NULL');

        $correcciones = [
            13701015 => ['tasa_interes' => 0.0100, 'tasa_interes_alt' => 0.0090], // Rapi-Crédito Libre Inversión
            13701017 => ['tasa_interes' => 0.0040, 'tasa_interes_alt' => null],   // Rapi-Crédito Educativo
            13701021 => ['tasa_interes' => 0.0100, 'tasa_interes_alt' => null],   // Rapi-Crédito Vehículo
            13701022 => ['tasa_interes' => 0.0040, 'tasa_interes_alt' => null],   // Rapi-Crédito Salud
            13701010 => ['tasa_interes' => 0.0100, 'tasa_interes_alt' => null],   // Libre Inversión Menores
            13701011 => ['tasa_interes' => 0.0090, 'tasa_interes_alt' => null],   // Libre Inversión Mayores
            13701016 => ['tasa_interes' => 0.0080, 'tasa_interes_alt' => null],   // Créditos Hipotecarios
            13701023 => ['tasa_interes' => 0.0040, 'tasa_interes_alt' => null],   // Mi Primera Inversión
        ];

        foreach ($correcciones as $cuenta => $tasas) {
            DB::table('cre_lineas_creditos')->where('cuenta', $cuenta)->update($tasas);
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE cre_lineas_creditos MODIFY tasa_interes DECIMAL(8,2) NULL');
        DB::statement('ALTER TABLE cre_lineas_creditos MODIFY tasa_interes_alt DECIMAL(8,2) NULL');
    }
};
