<?php

namespace App\Exports\Asociado;

use App\Models\Asociado\MaeAsociado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AsociadosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return MaeAsociado::select([
            'cedula', 'nombre1', 'nombre2', 'apellido1', 'apellido2',
            'fecha_nacimiento', 'lugar_expedicion_cedula', 'fecha_expedicion', 'estado_civil',
            'correo_pastor', 'celular_pastor', 'whatsapp',
            'fecha_afiliacion', 'distrito_actual', 'ciudad_distrito', 'direccion_distrito',
            'estado_pastor', 'especificacion', 'licencia', 'pais', 'iglesia_actual',
            'cedula_esposa', 'nombre_esposa', 'correo_esposa', 'celular_esposa',
            'doc_formulario_afiliacion', 'doc_autorizacion_datos', 'doc_cedula_pastor', 
            'doc_cedula_esposa', 'doc_licencia_pastoral', 'doc_registro_matrimonio', 'doc_id_hijos',
            'radicado', 'escaneado', 'cargado_ecm', 'ubicacion_ecm_link', 'validado_archivo', 
            'ubicacion_carpeta', 'numero_caja', 'cantidad_folios', 'fecha_ingreso_archivo', 
            'estado_conservacion', 'custodia_actual', 'observaciones_archivo', 'observaciones_generales', 'estado'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'cedula', 'nombre1', 'nombre2', 'apellido1', 'apellido2',
            'fecha_nacimiento', 'lugar_expedicion_cedula', 'fecha_expedicion', 'estado_civil',
            'correo_pastor', 'celular_pastor', 'whatsapp',
            'fecha_afiliacion', 'distrito_actual', 'ciudad_distrito', 'direccion_distrito',
            'estado_pastor', 'especificacion', 'licencia', 'pais', 'iglesia_actual',
            'cedula_esposa', 'nombre_esposa', 'correo_esposa', 'celular_esposa',
            'doc_formulario_afiliacion', 'doc_autorizacion_datos', 'doc_cedula_pastor', 
            'doc_cedula_esposa', 'doc_licencia_pastoral', 'doc_registro_matrimonio', 'doc_id_hijos',
            'radicado', 'escaneado', 'cargado_ecm', 'ubicacion_ecm_link', 'validado_archivo', 
            'ubicacion_carpeta', 'numero_caja', 'cantidad_folios', 'fecha_ingreso_archivo', 
            'estado_conservacion', 'custodia_actual', 'observaciones_archivo', 'observaciones_generales', 'estado'
        ];
    }
}