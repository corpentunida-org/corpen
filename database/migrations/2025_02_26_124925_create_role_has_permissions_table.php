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
        // Definir los nombres de las tablas y las columnas según el contexto
        $tableNames = config('permission.table_names');
        $pivotRole = 'role_id'; // Definir el nombre de la columna para el role
        $pivotPermission = 'permission_id'; // Definir el nombre de la columna para el permiso

        // Crear la tabla 'role_has_permissions'
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            // Definir las columnas
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            // Definir las claves foráneas
            $table->foreign($pivotPermission)
                ->references('id') // referencia al campo 'id' en la tabla 'permissions'
                ->on($tableNames['permissions'])
                ->onDelete('cascade'); // Eliminar en cascada cuando se elimine un permiso

            $table->foreign($pivotRole)
                ->references('id') // referencia al campo 'id' en la tabla 'roles'
                ->on($tableNames['roles'])
                ->onDelete('cascade'); // Eliminar en cascada cuando se elimine un rol

            // Definir la clave primaria compuesta
            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
    }
};
