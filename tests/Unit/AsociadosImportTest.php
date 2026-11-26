<?php

namespace Tests\Unit;

use App\Imports\Asociado\AsociadosImport;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class AsociadosImportTest extends TestCase
{
    public function test_it_maps_exported_headings_to_associate_fields(): void
    {
        $import = new AsociadosImport();
        $import->collection(new Collection([
            new Collection([
                'cedula' => '12345',
                'primer_nombre' => 'Ana',
                'segundo_nombre' => 'Maria',
                'primer_apellido' => 'Perez',
                'segundo_apellido' => 'Lopez',
                'codigo_distrito' => '01',
                'codigo_ciudad' => '101',
                'ubicacion_link_ecm' => 'https://example.test/expediente',
                'estado_consolidado' => 'Bueno',
            ]),
        ]));

        $row = $import->getRows()[0];

        $this->assertSame('Ana', $row['nombre1']);
        $this->assertSame('Maria', $row['nombre2']);
        $this->assertSame('Perez', $row['apellido1']);
        $this->assertSame('Lopez', $row['apellido2']);
        $this->assertSame('01', $row['distrito_actual']);
        $this->assertSame('101', $row['ciudad_distrito']);
        $this->assertSame('https://example.test/expediente', $row['ubicacion_ecm_link']);
        $this->assertSame('Bueno', $row['estado_conservacion']);
    }

    public function test_missing_name_columns_are_left_for_row_validation(): void
    {
        $import = new AsociadosImport();
        $import->collection(new Collection([
            new Collection(['cedula' => '12345']),
        ]));

        $row = $import->getRows()[0];

        $this->assertNull($row['nombre1']);
        $this->assertNull($row['apellido1']);
    }
}
