<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columnas solo si NO existen
        if (!Schema::hasColumn('car_sia_operaciones_lineas', 'id_factura')) {
            Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
                $table->unsignedBigInteger('id_factura')->nullable()->after('id_car_sia_operaciones');
            });
        }

        if (!Schema::hasColumn('car_sia_operaciones_lineas', 'payload_documento')) {
            Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
                $table->json('payload_documento')->nullable()->after('estadoApi');
            });
        }

        // 2. Cambiar tipo de columna
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            $table->integer('numero_bloque')->change();
        });

        // 3. Renombrar columnas si aún tienen el nombre viejo
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            if (Schema::hasColumn('car_sia_operaciones_lineas', 'id_cre_lineas_creditos')) {
                $table->renameColumn('id_cre_lineas_creditos', 'id_car_sia_lineas');
            }

            if (Schema::hasColumn('car_sia_operaciones_lineas', 'id_car_sia_estados_operacion')) {
                $table->renameColumn('id_car_sia_estados_operacion', 'id_car_sia_estados');
            }
        });

        // 4. Crear las llaves foráneas validando previamente si ya existen en la base de datos
        $databaseName = DB::getDatabaseName();

        $fkLineasExists = DB::selectOne("
            SELECT COUNT(*) as count
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
              AND TABLE_NAME = 'car_sia_operaciones_lineas'
              AND CONSTRAINT_NAME = 'car_sia_operaciones_lineas_id_car_sia_lineas_foreign'
        ", [$databaseName])->count > 0;

        $fkEstadosExists = DB::selectOne("
            SELECT COUNT(*) as count
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
              AND TABLE_NAME = 'car_sia_operaciones_lineas'
              AND CONSTRAINT_NAME = 'car_sia_operaciones_lineas_id_car_sia_estados_foreign'
        ", [$databaseName])->count > 0;

        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) use ($fkLineasExists, $fkEstadosExists) {
            if (!$fkLineasExists) {
                $table->foreign('id_car_sia_lineas')->references('id')->on('cre_lineas_creditos');
            }

            if (!$fkEstadosExists) {
                $table->foreign('id_car_sia_estados')->references('id')->on('car_sia_estados_operacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            if (Schema::hasColumn('car_sia_operaciones_lineas', 'id_car_sia_lineas')) {
                $table->renameColumn('id_car_sia_lineas', 'id_cre_lineas_creditos');
            }

            if (Schema::hasColumn('car_sia_operaciones_lineas', 'id_car_sia_estados')) {
                $table->renameColumn('id_car_sia_estados', 'id_car_sia_estados_operacion');
            }
        });

        Schema::table('car_sia_operaciones_lineas', function (Blueprint $table) {
            if (Schema::hasColumn('car_sia_operaciones_lineas', 'payload_documento')) {
                $table->dropColumn('payload_documento');
            }

            $table->string('numero_bloque', 50)->change();
        });
    }
};
