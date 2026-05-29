<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaeAsociadoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta petición.
     */
    public function authorize()
    {
        return true; // Cambiar a false e implementar lógica de roles/permisos si es necesario
    }

    /**
     * Obtiene las reglas de validación que se aplican a la petición.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            // Gestión de Archivo Físico
            'radicado'                  => 'nullable|string|max:50',
            
            // Datos de Identidad y Demográficos
            'cedula'                    => 'required|string|max:20|unique:mae_asociados,cedula',
            'nombre1'                   => 'required|string|max:50',
            'nombre2'                   => 'nullable|string|max:50',
            'apellido1'                 => 'required|string|max:50',
            'apellido2'                 => 'nullable|string|max:50',
            'fecha_nacimiento'          => 'nullable|date|before:today',
            'lugar_expedicion_cedula'   => 'nullable|string|max:100',
            'fecha_expedicion'          => 'nullable|date|before_or_equal:today',
            'estado_civil'              => 'nullable|string|in:Soltero,Casado,Viudo',
            
            // Datos de Contacto
            'correo_pastor'             => 'nullable|email|max:100',
            'celular_pastor'            => 'nullable|string|max:20',
            'whatsapp'                  => 'nullable|string|max:20',
            
            // Información Ministerial y Corporativa
            'fecha_afiliacion'          => 'nullable|date',
            'distrito_actual'           => 'nullable|string|max:100',
            'ciudad_distrito'           => 'nullable|string|max:100',
            'direccion_distrito'        => 'nullable|string|max:150',
            'estado_pastor'             => 'nullable|string|in:Activo,Retirado,Licencia,Suspendido',
            'especificacion'            => 'nullable|string|max:100',
            'licencia'                  => 'nullable|string|max:50',
            'pais'                      => 'nullable|string|max:50',
            'iglesia_actual'            => 'nullable|string|max:150',
            
            // Información Familiar (Cónyuge)
            'cedula_esposa'             => 'nullable|string|max:20',
            'nombre_esposa'             => 'nullable|string|max:100',
            'correo_esposa'             => 'nullable|email|max:100',
            'celular_esposa'            => 'nullable|string|max:20',
            
            // Soportes Documentales (Anexos)
            'doc_formulario_afiliacion' => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_autorizacion_datos'    => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_cedula_pastor'         => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_cedula_esposa'         => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_licencia_pastoral'     => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_registro_matrimonio'   => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            'doc_id_hijos'              => 'nullable|string|in:Pendiente,Entregado,No Aplica',
            
            // Gestión de Archivo Digital (ECM)
            'escaneado'                 => 'nullable|boolean',
            'cargado_ecm'               => 'nullable|boolean',
            'validado_archivo'          => 'nullable|boolean',
            'ubicacion_ecm_link'        => 'nullable|url|max:255',
            
            // Gestión de Archivo Físico (Complemento)
            'ubicacion_carpeta'         => 'nullable|string|max:100',
            'numero_caja'               => 'nullable|string|max:50',
            'cantidad_folios'           => 'nullable|integer|min:0',
            'fecha_ingreso_archivo'     => 'nullable|date',
            'estado_conservacion'       => 'nullable|string|in:Bueno,Regular,Malo',
            'custodia_actual'           => 'nullable|string|max:100',
            'observaciones_archivo'     => 'nullable|string|max:1000',
            
            // Metadatos y Auditoría
            'observaciones_generales'   => 'nullable|string|max:1000',
            'estado'                    => 'nullable|string|in:Activo,Inactivo',
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas de validación.
     */
    public function messages()
    {
        return [
            // Identidad
            'cedula.required'           => 'El número de cédula es un campo obligatorio.',
            'cedula.unique'             => 'Esta cédula ya se encuentra registrada en otro expediente.',
            'cedula.max'                => 'La cédula no puede superar los 20 caracteres.',
            'nombre1.required'          => 'El primer nombre es obligatorio.',
            'apellido1.required'        => 'El primer apellido es obligatorio.',
            'fecha_nacimiento.before'   => 'La fecha de nacimiento no puede ser una fecha futura.',
            'fecha_expedicion.before_or_equal' => 'La fecha de expedición no puede ser una fecha futura.',
            'estado_civil.in'           => 'El estado civil seleccionado no es válido.',

            // Contacto
            'correo_pastor.email'       => 'Debe ingresar un formato de correo electrónico válido.',
            'correo_pastor.max'         => 'El correo electrónico es demasiado largo.',

            // Ministerial
            'estado_pastor.in'          => 'El estado del pastor seleccionado no es válido.',

            // Familia
            'correo_esposa.email'       => 'El correo de la esposa debe tener un formato válido.',

            // Validaciones Documentales
            'doc_formulario_afiliacion.in' => 'El estado del Formulario de Afiliación no es válido.',
            'doc_autorizacion_datos.in' => 'El estado de la Autorización de Datos no es válido.',

            // Archivo / ECM
            'ubicacion_ecm_link.url'    => 'El enlace del ECM debe ser una URL válida (ej. https://...).',
            'cantidad_folios.integer'   => 'La cantidad de folios debe ser un número entero.',
            'cantidad_folios.min'       => 'La cantidad de folios no puede ser negativa.',
            'estado_conservacion.in'    => 'El estado de conservación seleccionado no es válido.',
            'observaciones_archivo.max' => 'Las observaciones de archivo exceden el límite permitido.',
            
            // General
            'observaciones_generales.max' => 'Las observaciones generales exceden el límite permitido.',
            'estado.in'                 => 'El estado general del expediente no es válido.',
        ];
    }
}