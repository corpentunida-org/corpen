<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('inv_activos', function (Blueprint $table) {
            $table->id();
            
            // 1. Identificación Básica
            $table->string('nombre'); // Ej: Portátil Dell Latitude
            $table->string('codigo_activo')->nullable()->unique(); // Placa de inventario interna
            $table->string('serial')->nullable(); // Serial del fabricante
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida')->default('UNIDAD');
            $table->string('hoja_vida')->nullable(); // Ruta del archivo PDF
            
            // 2. Clasificación y Ubicación (Foreign Keys)
            $table->foreignId('id_InvSubGrupos')->constrained('inv_subgrupos');
            $table->foreignId('id_InvMarcas')->constrained('inv_marcas');
            $table->foreignId('id_InvBodegas')->constrained('inv_bodegas');
            
            // YA EXISTA en tu base de datos
            // Usamos Integer normal para que sea compatible
            $table->unsignedInteger('id_MaeMunicipios'); 
            $table->foreign('id_MaeMunicipios')->references('id')->on('MaeMunicipios');

            // 3. Trazabilidad de Origen (¿De dónde salió?)
            $table->foreignId('id_InvDetalleCompras')->constrained('inv_detalle_compras');
            $table->foreignId('id_usersRegistro')->constrained('users'); // Quién lo creó en el sistema

            // 4. Gestión de Ciclo de Vida y Garantías
            $table->date('fecha_inicio_garantia')->nullable();
            $table->date('fecha_fin_garantia')->nullable();
            $table->integer('vida_util_meses')->nullable(); // Lo puse numérico para cálculos matemáticos
            
            // 5. Estado Actual (En Caliente)
            $table->foreignId('id_Estado')->default(1)->constrained('inv_estados'); // 1 = Disponible (por defecto)
            $table->foreignId('id_ultimo_usuario_asignado')->nullable()->constrained('users'); // Quién lo tiene AHORA

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_activos');
    }
};
