<?php

namespace App\Imports\Asociado;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsociadosImport implements ToCollection, WithHeadingRow
{
    protected $rows = [];

    /**
     * Recorre la colección leída del Excel, limpia los datos, formatea fechas 
     * y las guarda en el array en memoria.
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Omitir si la cédula viene vacía en el Excel
            if (empty(trim($row['cedula'] ?? ''))) {
                continue;
            }

            // Mapeo exhaustivo de las 48 columnas con transformación en caliente
            $this->rows[] = [
                'radicado'                  => $row['radicado'] ?? null,
                'cedula'                    => $row['cedula'],
                'nombre1'                   => $row['nombre1'],
                'nombre2'                   => $row['nombre2'] ?? null,
                'apellido1'                 => $row['apellido1'],
                'apellido2'                 => $row['apellido2'] ?? null,
                'fecha_nacimiento'          => $this->transformDate($row['fecha_nacimiento'] ?? null),
                'lugar_expedicion_cedula'   => $row['lugar_expedicion_cedula'] ?? null,
                'fecha_expedicion'          => $this->transformDate($row['fecha_expedicion'] ?? null),
                'estado_civil'              => $row['estado_civil'] ?? null,
                'correo_pastor'             => $row['correo_pastor'] ?? null,
                'celular_pastor'            => $row['celular_pastor'] ?? null,
                'whatsapp'                  => $row['whatsapp'] ?? null,
                'fecha_afiliacion'          => $this->transformDate($row['fecha_afiliacion'] ?? null),
                'distrito_actual'           => $row['distrito_actual'] ?? null,
                'ciudad_distrito'           => $row['ciudad_distrito'] ?? null,
                'direccion_distrito'        => $row['direccion_distrito'] ?? null,
                'estado_pastor'             => $row['estado_pastor'] ?? null,
                'especificacion'            => $row['especificacion'] ?? null,
                'licencia'                  => $row['licencia'] ?? null,
                'pais'                      => $row['pais'] ?? 'Colombia',
                'iglesia_actual'            => $row['iglesia_actual'] ?? null,
                'cedula_esposa'             => $row['cedula_esposa'] ?? null,
                'nombre_esposa'             => $row['nombre_esposa'] ?? null,
                'correo_esposa'             => $row['correo_esposa'] ?? null,
                'celular_esposa'            => $row['celular_esposa'] ?? null,
                'doc_formulario_afiliacion' => $row['doc_formulario_afiliacion'] ?? null,
                'doc_autorizacion_datos'    => $row['doc_autorizacion_datos'] ?? null,
                'doc_cedula_pastor'         => $row['doc_cedula_pastor'] ?? null,
                'doc_cedula_esposa'         => $row['doc_cedula_esposa'] ?? null,
                'doc_licencia_pastoral'     => $row['doc_licencia_pastoral'] ?? null,
                'doc_registro_matrimonio'   => $row['doc_registro_matrimonio'] ?? null,
                'doc_id_hijos'              => $row['doc_id_hijos'] ?? null,
                
                // Lógica inteligente para booleanos: Lee el excel o lo infiere
                'escaneado'                 => $this->parseBoolean($row['escaneado'] ?? (!empty($row['radicado']))),
                'cargado_ecm'               => $this->parseBoolean($row['cargado_ecm'] ?? (!empty($row['ubicacion_ecm_link']))),
                'validado_archivo'          => $this->parseBoolean($row['validado_archivo'] ?? false),
                'ubicacion_ecm_link'        => $row['ubicacion_ecm_link'] ?? null,
                
                'ubicacion_carpeta'         => $row['ubicacion_carpeta'] ?? null,
                'numero_caja'               => $row['numero_caja'] ?? null,
                'cantidad_folios'           => $row['cantidad_folios'] ?? 0,
                'fecha_ingreso_archivo'     => $this->transformDate($row['fecha_ingreso_archivo'] ?? null),
                'estado_conservacion'       => $row['estado_conservacion'] ?? null,
                'custodia_actual'           => $row['custodia_actual'] ?? null,
                'observaciones_archivo'     => $row['observaciones_archivo'] ?? null,
                'observaciones_generales'   => $row['observaciones_generales'] ?? null,
                'estado'                    => $row['estado'] ?? 'Activo',
            ];
        }
    }

    /**
     * Retorna el array procesado al Controlador
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Helper para formateo de fechas
     */
    private function transformDate($value, $format = 'Y-m-d')
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
            }
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return null; 
        }
    }

    /**
     * Helper para interpretar valores de Excel como booleanos en MySQL
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) return $value;
        if (is_string($value)) {
            $value = strtoupper(trim($value));
            if (in_array($value, ['1', 'SI', 'SÍ', 'TRUE', 'VERDADERO', 'V'])) return true;
        }
        if ($value == 1) return true;
        
        return false;
    }
}