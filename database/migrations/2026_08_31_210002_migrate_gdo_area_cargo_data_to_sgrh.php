<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Copia única de gdo_area/gdo_cargo (módulo Archivo/"Gestión") hacia sgrh_areas/sgrh_cargos.
 * gdo_area/gdo_cargo NO se tocan ni se borran — siguen en uso por User/Interaction hasta que
 * ese módulo se retire por separado. Esta migración solo puebla las tablas nuevas.
 */
return new class extends Migration
{
    public function up(): void
    {
        $areaIdMap = [];
        foreach (DB::table('gdo_area')->orderBy('id')->get() as $area) {
            $areaIdMap[$area->id] = DB::table('sgrh_areas')->insertGetId([
                'nombre' => trim($area->nombre),
                'descripcion' => $area->descripcion,
                'activo' => $area->estado === 'activo',
                'created_at' => $area->created_at,
                'updated_at' => $area->updated_at,
            ]);
        }

        $cargoIdMap = [];
        foreach (DB::table('gdo_cargo')->orderBy('id')->get() as $cargo) {
            $cargoIdMap[$cargo->id] = DB::table('sgrh_cargos')->insertGetId([
                'nombre' => $cargo->nombre_cargo,
                'sgrh_area_id' => $areaIdMap[$cargo->GDO_area_id] ?? null,
                'salario_base' => $cargo->salario_base,
                'jornada' => $cargo->jornada,
                'telefono_corporativo' => $cargo->telefono_corporativo,
                'celular_corporativo' => $cargo->celular_corporativo,
                'ext_corporativo' => $cargo->ext_corporativo,
                'correo_corporativo' => $cargo->correo_corporativo,
                'gmail_corporativo' => $cargo->gmail_corporativo ?: null,
                'manual_funciones' => $cargo->manual_funciones,
                'observaciones' => $cargo->observacion,
                'activo' => (bool) $cargo->estado,
                'created_at' => $cargo->created_at,
                'updated_at' => $cargo->updated_at,
            ]);
        }

        foreach (DB::table('gdo_area')->orderBy('id')->get() as $area) {
            if ($area->GDO_cargo_id && isset($cargoIdMap[$area->GDO_cargo_id])) {
                DB::table('sgrh_areas')
                    ->where('id', $areaIdMap[$area->id])
                    ->update(['cargo_responsable_id' => $cargoIdMap[$area->GDO_cargo_id]]);
            }
        }
    }

    public function down(): void
    {
        // No reversible: es una copia de datos, no una migración estructural.
        // Revertir borraría áreas/cargos que ya pudieron editarse desde SGRH tras la copia.
    }
};
