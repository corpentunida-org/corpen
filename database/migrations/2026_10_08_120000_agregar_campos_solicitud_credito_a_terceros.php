<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Campos que pide el formulario físico "Solicitud de Crédito de Alta Cuantía" y que no existían
 * en MaeTerceros. La mayoría de los datos personales del formulario YA existían (verificado con
 * datos reales): nombre=nom_ter, nacimiento=fec_nac, celular=cel, dirección=dir,
 * municipio=ciudad, departamento=depa, correo=email, fecha ingreso Corpentunida=fec_ing, fecha
 * ingreso ministerio=fec_minis, cónyuge=nom_conyug/id_conyuge/mail_conyu, hijos=num_hijos.
 * Solo faltan los de salud/vivienda y un par de contacto adicionales.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MaeTerceros (26.209 filas, 160 columnas de un ERP viejo) ya tiene datos inválidos en
        // columnas sin relación (ej. fechas '0000-00-00') que el modo estricto de MySQL
        // revalida en un ALTER TABLE normal. Un solo ALTER con ALGORITHM=INPLACE evita que
        // MySQL reescriba/revalide las filas existentes — solo agrega metadata de columna.
        $nuevas = [
            'whatsapp' => "whatsapp varchar(20) null",
            'cel_conyu' => "cel_conyu varchar(20) null",
            'personas_cargo' => "personas_cargo int unsigned null",
            'peso' => "peso decimal(5,2) null comment 'Kg'",
            'estatura' => "estatura decimal(5,2) null comment 'Metros'",
            'eps' => "eps varchar(100) null",
            'detalle_enfermedades' => "detalle_enfermedades text null",
            'tipo_vivienda' => "tipo_vivienda varchar(20) null comment 'propia | pastoral'",
            'congregacion_paga_servicios' => "congregacion_paga_servicios tinyint(1) null",
            'congregacion_paga_arriendo' => "congregacion_paga_arriendo tinyint(1) null",
            'congregacion_paga_otros' => "congregacion_paga_otros varchar(255) null",
        ];

        $faltantes = array_filter(array_keys($nuevas), fn ($c) => !Schema::hasColumn('MaeTerceros', $c));
        if (!empty($faltantes)) {
            $clausulas = implode(', ', array_map(fn ($c) => 'ADD COLUMN ' . $nuevas[$c], $faltantes));
            // El índice FULLTEXT de la tabla obliga a MySQL a usar ALGORITHM=COPY, que revalida
            // TODAS las filas existentes — y una fila preexistente tiene fec_expcc en
            // '0000-00-00' (dato sucio ajeno a este cambio). Se relaja el sql_mode solo para
            // esta sesión/statement, sin tocar ningún dato ni el sql_mode global.
            $sqlModeOriginal = DB::select('SELECT @@session.sql_mode as m')[0]->m;
            DB::statement("SET SESSION sql_mode = ''");
            try {
                DB::statement("ALTER TABLE `MaeTerceros` {$clausulas}");
            } finally {
                DB::statement("SET SESSION sql_mode = '{$sqlModeOriginal}'");
            }
        }

        if (!Schema::hasTable('cre_referencias')) {
            Schema::create('cre_referencias', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cod_ter')->index();
                $table->string('tipo', 20); // familiar | comercial
                $table->string('nombre', 150);
                $table->string('cedula', 20)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('relacion', 100)->nullable();
                $table->text('observacion')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cre_referencias');

        $cols = ['whatsapp', 'cel_conyu', 'personas_cargo', 'peso', 'estatura', 'eps', 'detalle_enfermedades', 'tipo_vivienda', 'congregacion_paga_servicios', 'congregacion_paga_arriendo', 'congregacion_paga_otros'];
        $existentes = array_filter($cols, fn ($c) => Schema::hasColumn('MaeTerceros', $c));
        if (!empty($existentes)) {
            $clausulas = implode(', ', array_map(fn ($c) => "DROP COLUMN `{$c}`", $existentes));
            $sqlModeOriginal = DB::select('SELECT @@session.sql_mode as m')[0]->m;
            DB::statement("SET SESSION sql_mode = ''");
            try {
                DB::statement("ALTER TABLE `MaeTerceros` {$clausulas}");
            } finally {
                DB::statement("SET SESSION sql_mode = '{$sqlModeOriginal}'");
            }
        }
    }
};
