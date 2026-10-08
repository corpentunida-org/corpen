<?php

namespace App\Exports\Asociado;

use App\Models\Asociado\MaeAsociado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AsociadosExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    /**
    * Usamos FromQuery en lugar de FromCollection para procesar los datos por lotes (Chunks)
    * y evitar que la memoria RAM colapse.
    */
    public function query()
    {
        return MaeAsociado::query()->with(['ciudad', 'distrito', 'tercero']);
    }

    /**
    * Define el tamaño de cada bloque que se procesará en memoria (ej. 500 filas por lote)
    */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
    * Mapea cada fila del asociado de forma segura
    */
    public function map($asociado): array
    {
        return [
            // Identidad y Demográficos
            $asociado->radicado,
            $asociado->cedula,
            $asociado->nombre1,
            $asociado->nombre2,
            $asociado->apellido1,
            $asociado->apellido2,
            optional($asociado->fecha_nacimiento)->format('Y-m-d'),
            $asociado->lugar_expedicion_cedula,
            optional($asociado->fecha_expedicion)->format('Y-m-d'),
            $asociado->estado_civil,

            // Contacto
            $asociado->correo_pastor,
            $asociado->celular_pastor,
            $asociado->whatsapp,

            // Información Ministerial y Corporativa
            optional($asociado->fecha_afiliacion)->format('Y-m-d'),
            $asociado->distrito_actual,
            optional($asociado->distrito)->NOM_DIST,
            $asociado->ciudad_distrito,
            optional($asociado->ciudad)->nombre,
            $asociado->direccion_distrito,
            $asociado->estado_pastor,
            $asociado->especificacion,
            $asociado->licencia,
            $asociado->pais,
            $asociado->iglesia_actual,

            // Información Familiar (Cónyuge)
            $asociado->cedula_esposa,
            $asociado->nombre_esposa,
            $asociado->correo_esposa,
            $asociado->celular_esposa,

            // Soportes Documentales (Anexos)
            $asociado->doc_formulario_afiliacion,
            $asociado->doc_autorizacion_datos,
            $asociado->doc_cedula_pastor,
            $asociado->doc_cedula_esposa,
            $asociado->doc_licencia_pastoral,
            $asociado->doc_registro_matrimonio,
            $asociado->doc_id_hijos,

            // Gestión de Archivo Digital (ECM)
            $asociado->escaneado ? 'Sí' : 'No',
            $asociado->cargado_ecm ? 'Sí' : 'No',
            $asociado->ubicacion_ecm_link,
            $asociado->validado_archivo ? 'Sí' : 'No',

            // Gestión de Archivo Físico
            $asociado->ubicacion_carpeta,
            $asociado->numero_caja,
            $asociado->cantidad_folios,
            optional($asociado->fecha_ingreso_archivo)->format('Y-m-d'),
            $asociado->estado_conservacion,
            $asociado->custodia_actual,
            $asociado->observaciones_archivo,

            // Metadatos y Auditoría
            $asociado->observaciones_generales,
            $asociado->estado,
        ];
    }

    /**
    * Encabezados correspondientes a cada columna exportada
    */
    public function headings(): array
    {
        return [
            'Radicado', 'Cédula', 'Primer Nombre', 'Segundo Nombre', 'Primer Apellido', 'Segundo Apellido',
            'Fecha Nacimiento', 'Lugar Expedición Cédula', 'Fecha Expedición', 'Estado Civil',
            'Correo Pastor', 'Celular Pastor', 'WhatsApp',
            'Fecha Afiliación', 'Código Distrito', 'Nombre Distrito', 'Código Ciudad', 'Nombre Ciudad',
            'Dirección Distrito', 'Estado Pastor', 'Especificación', 'Licencia', 'País', 'Iglesia Actual',
            'Cédula Esposa', 'Nombre Esposa', 'Correo Esposa', 'Celular Esposa',
            'Doc Formulario Afiliación', 'Doc Autorización Datos', 'Doc Cédula Pastor', 'Doc Cédula Esposa',
            'Doc Licencia Pastoral', 'Doc Registro Matrimonio', 'Doc ID Hijos',
            'Escaneado', 'Cargado ECM', 'Ubicación Link ECM', 'Validado Archivo',
            'Ubicación Carpeta', 'Número Caja', 'Cantidad Folios', 'Fecha Ingreso Archivo',
            'Estado Consolidado', 'Custodia Actual', 'Observaciones Archivo',
            'Observaciones Generales', 'Estado'
        ];
    }
}
