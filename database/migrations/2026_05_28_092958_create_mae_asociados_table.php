<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mae_asociados', function (Blueprint $table) {
            // =========================================================
            // 1. Metadatos del Sistema y Auditoría 
            // =========================================================
            $table->id()->comment('Identificador único incremental y automático del registro');

            // =========================================================
            // 2. Gestión de Archivo Físico (Identificación) 
            // =========================================================
            $table->string('radicado', 20)->nullable()->comment('Número consecutivo de radicación o expediente físico asignado');

            // =========================================================
            // 3. Datos de Identidad y Demográficos 
            // =========================================================
            $table->string('cedula', 15)->unique()->comment('Número de documento de identidad oficial del pastor');
            $table->string('nombre1', 100)->comment('Primer nombre');
            $table->string('nombre2', 100)->nullable()->comment('Segundo nombre');
            $table->string('apellido1', 100)->comment('Primer apellido');
            $table->string('apellido2', 100)->nullable()->comment('Segundo apellido');
            $table->date('fecha_nacimiento')->nullable()->comment('Fecha de nacimiento completa del pastor');
            $table->string('lugar_expedicion_cedula', 50)->nullable()->comment('Municipio o ciudad donde fue expedido el documento');
            $table->date('fecha_expedicion')->nullable()->comment('Fecha de expedición del documento de identidad');
            $table->string('estado_civil', 20)->nullable()->comment('Estado civil actual del pastor (Casado, Soltero, etc.)');

            // =========================================================
            // 4. Datos de Contacto 
            // =========================================================
            $table->string('correo_pastor', 100)->nullable()->comment('Dirección de correo electrónico principal de contacto');
            $table->string('celular_pastor', 15)->nullable()->comment('Número de teléfono celular principal');
            $table->string('whatsapp', 15)->nullable()->comment('Número de WhatsApp habilitado para comunicaciones oficiales');

            // =========================================================
            // 5. Información Ministerial y Corporativa 
            // =========================================================
            $table->date('fecha_afiliacion')->nullable()->comment('Fecha de ingreso formal o afiliación a la organización');
            $table->string('distrito_actual', 50)->nullable()->comment('Nombre del distrito eclesiástico o administrativo asignado');
            $table->string('ciudad_distrito', 50)->nullable()->comment('Ciudad sede de la iglesia o del distrito correspondiente');
            $table->string('direccion_distrito', 100)->nullable()->comment('Dirección física de la sede o iglesia actual');
            $table->string('estado_pastor', 20)->nullable()->comment('Estado operativo actual (Activo, Licencia, Retirado, Suspendido)');
            $table->string('especificacion', 50)->nullable()->comment('Especialidad o rol ministerial asignado dentro de la estructura');
            $table->string('licencia', 30)->nullable()->comment('Grado o tipo de licencia pastoral otorgada por la organización');
            $table->string('pais', 30)->nullable()->comment('País donde ejerce sus funciones o reside actualmente');
            $table->string('iglesia_actual', 100)->nullable()->comment('Nombre oficial de la congregación o iglesia asignada actualmente');

            // =========================================================
            // 6. Información Familiar (Cónyuge) 
            // =========================================================
            $table->string('cedula_esposa', 15)->nullable()->comment('Número de documento de identidad de la esposa');
            $table->string('nombre_esposa', 100)->nullable()->comment('Nombre completo de la esposa del pastor');
            $table->string('correo_esposa', 100)->nullable()->comment('Correo electrónico de contacto de la esposa');
            $table->string('celular_esposa', 15)->nullable()->comment('Número de teléfono móvil de la esposa');

            // =========================================================
            // 7. Soportes Documentales (Anexos) 
            // Nota: Se usa VARCHAR(20) para admitir estados como "Entregado", "Pendiente", "No Aplica" como se muestra en el PDF.
            // =========================================================
            $table->string('doc_formulario_afiliacion', 20)->nullable()->comment('Soporte o estado del formulario físico/digital de afiliación');
            $table->string('doc_autorizacion_datos', 20)->nullable()->comment('Soporte físico o digital de la autorización de Habeas Data');
            $table->string('doc_cedula_pastor', 20)->nullable()->comment('Copia del documento de identidad del pastor en el expediente');
            $table->string('doc_cedula_esposa', 20)->nullable()->comment('Copia del documento de identidad de la esposa en el expediente');
            $table->string('doc_licencia_pastoral', 20)->nullable()->comment('Copia del documento que avala la licencia pastoral actual');
            $table->string('doc_registro_matrimonio', 20)->nullable()->comment('Copia del registro civil de matrimonio o acta eclesiástica');
            $table->string('doc_id_hijos', 20)->nullable()->comment('Copia de los documentos de identidad de los hijos (si aplica)');

            // =========================================================
            // 8. Gestión de Archivo Digital (ECM) 
            // =========================================================
            $table->boolean('escaneado')->default(false)->comment('Bandera lógica que indica si el documento físico ya fue digitalizado');
            $table->boolean('cargado_ecm')->default(false)->comment('Indica si los archivos digitalizados fueron subidos al gestor documental');
            $table->string('ubicacion_ecm_link', 255)->nullable()->comment('Enlace web (URL) directo al expediente electrónico en la plataforma');
            $table->boolean('validado_archivo')->default(false)->comment('Indica si el expediente digitalizado fue revisado y aprobado por auditoría');

            // =========================================================
            // 9. Gestión de Archivo Físico 
            // =========================================================
            $table->string('ubicacion_carpeta', 30)->nullable()->comment('Código o identificación del estante/carpeta física en el archivo');
            $table->string('numero_caja', 20)->nullable()->comment('Número o identificador de la caja de archivo definitivo');
            $table->integer('cantidad_folios')->nullable()->comment('Número total de hojas o folios útiles dentro de la carpeta física');
            $table->date('fecha_ingreso_archivo')->nullable()->comment('Fecha en que se archivó físicamente el expediente completo');
            $table->string('estado_conservacion', 30)->nullable()->comment('Evaluación del estado del papel físico (Bueno, Regular, Malo)');
            $table->string('custodia_actual', 50)->nullable()->comment('Departamento, área o persona que tiene la custodia del expediente');
            $table->text('observaciones_archivo')->nullable()->comment('Notas específicas respecto al estado o incidencias del archivo físico');

            // =========================================================
            // 10. Observaciones Generales y Timestamps 
            // =========================================================
            $table->text('observaciones_generales')->nullable()->comment('Comentarios de carácter general o alertas sobre la situación del pastor');
            
            // created_at y updated_at automáticos de Laravel
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mae_asociados');
    }
};