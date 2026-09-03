<?php

use App\Models\Sgrh\Contrato;
use App\Models\Sgrh\TipoContrato;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// El contrato pasa a ser la única fuente de fecha de ingreso y salario del colaborador — ya
// no se editan directo en Empleado (Empleado::getFechaIngresoAttribute()/
// getSalarioAsignadoAttribute() las derivan de contratoActivo). Antes de borrar las columnas,
// se preserva el dato de cualquier colaborador que tenga fecha_ingreso/salario_asignado
// cargados pero SIN ningún contrato todavía, creándole uno con esos valores (tipo
// "Indefinido" por defecto, ya que no se conoce si era realmente a término fijo ni su fecha
// de vencimiento real — queda editable después). Colaboradores que ya tienen contrato no se
// tocan: esa es la fuente real, y el valor viejo en Empleado puede ser dato de prueba.
return new class extends Migration
{
    public function up(): void
    {
        $tipoIndefinido = TipoContrato::where('nombre', 'Indefinido')->value('id');

        DB::table('sgrh_empleados')
            ->whereNotNull('fecha_ingreso')
            ->get(['id', 'fecha_ingreso', 'salario_asignado', 'cargo_id'])
            ->each(function ($empleado) use ($tipoIndefinido) {
                $tieneContrato = Contrato::where('empleado_id', $empleado->id)->exists();

                if (! $tieneContrato && $tipoIndefinido) {
                    Contrato::create([
                        'empleado_id' => $empleado->id,
                        'tipo_contrato_id' => $tipoIndefinido,
                        'cargo_id' => $empleado->cargo_id,
                        'fecha_inicio' => $empleado->fecha_ingreso,
                        'fecha_vencimiento' => null,
                        'estado' => 'Activo',
                        'salario_contrato' => $empleado->salario_asignado,
                        'observaciones' => 'Contrato generado automáticamente al migrar fecha_ingreso/salario_asignado desde Empleado (tipo asumido como Indefinido — verificar y corregir si aplica).',
                    ]);
                }
            });

        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->dropColumn(['fecha_ingreso', 'salario_asignado']);
        });
    }

    public function down(): void
    {
        Schema::table('sgrh_empleados', function (Blueprint $table) {
            $table->date('fecha_ingreso')->nullable()->after('cod_ter');
            $table->decimal('salario_asignado', 12, 2)->nullable()->after('cargo_id');
        });
    }
};
