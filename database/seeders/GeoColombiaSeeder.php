<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GeoColombiaSeeder extends Seeder
{
    public function run()
    {
        // 1. Insertar el País
        DB::table('geo_paises')->insertOrIgnore([
            'codigo_iso' => 'COL',
            'nombre'     => 'Colombia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Leer tu archivo JSON
        $rutaJson = database_path('data/colombia.json');
        
        if (!File::exists($rutaJson)) {
            $this->command->error("No se encontró el archivo en: {$rutaJson}");
            return;
        }

        $json = File::get($rutaJson);
        $departamentos = json_decode($json, true);

        $idCiudadGoblal = 1;

        foreach ($departamentos as $departamento) {
            // Ajustamos el ID para que empiece en 1 (ya que en tu JSON empieza en 0)
            $idRegion = $departamento['id'] + 1; 
            $nombreDepartamento = $departamento['departamento'];

            // 3. Insertar la Región (Departamento)
            DB::table('geo_regiones')->insert([
                'id_region'  => $idRegion,
                'nombre'     => $nombreDepartamento,
                'codigo_iso' => 'CO-' . strtoupper(substr($nombreDepartamento, 0, 3)),
                'iso_pais'   => 'COL',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Insertar la Subregión por defecto
            DB::table('geo_subregiones')->insert([
                'id_subregion' => $idRegion,
                'nombre'       => 'Subregión de ' . $nombreDepartamento,
                'id_region'    => $idRegion,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // 5. Preparar las Ciudades (Municipios)
            $ciudadesInsert = [];
            foreach ($departamento['ciudades'] as $ciudad) {
                $ciudadesInsert[] = [
                    'id_ciudad'    => $idCiudadGoblal,
                    'nombre'       => $ciudad, // PHP decodifica los \u00f1 automáticamente
                    'id_subregion' => $idRegion,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
                $idCiudadGoblal++;
            }
            
            // 6. Insertar ciudades en bloques para evitar saturar la memoria
            $chunks = array_chunk($ciudadesInsert, 500);
            foreach ($chunks as $chunk) {
                DB::table('geo_ciudades')->insert($chunk);
            }
        }
        
        $this->command->info('¡Toda la base de datos de Colombia ha sido poblada con éxito!');
    }
}