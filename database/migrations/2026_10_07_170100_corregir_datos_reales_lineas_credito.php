<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrige monto_maximo/monto_minimo/plazo/tasa/edad de las líneas de crédito que hoy tienen
 * datos de relleno (5.000.000 / 500.000 / 1 mes / 1% uniforme en casi todas), usando las reglas
 * reales de negocio ("Líneas de Créditos y sus Generalidades" + "Créditos Hipotecarios y
 * Mi Primera Inversión" que compartió el usuario). Identifica cada línea por su `cuenta`
 * (código contable), no por nombre, porque es el campo estable.
 *
 * monto_maximo/monto_minimo se dejan en NULL cuando el valor real es variable (ej: "80% del
 * fondo de retiro", "costo de la póliza") en vez de un monto fijo — el detalle queda en
 * `observacion`. edad_minima/edad_maxima se dejan en NULL cuando la línea no tiene restricción
 * de edad documentada, en vez de conservar el placeholder genérico anterior.
 *
 * Solo toca las 8 líneas para las que hay dato real confirmado; no inventa cuenta/tipo/garantía
 * para líneas nuevas (Ampliación de Hipotecarios, Retanqueo Hipotecario) que aún no existen.
 */
return new class extends Migration
{
    private function lineas(): array
    {
        return [
            // cuenta => [datos, documentos[]]
            13701015 => [ // Rapi-Crédito Libre Inversión
                'datos' => [
                    'monto_maximo' => 10000000,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 36,
                    'tasa_interes' => 0.01,
                    'tasa_interes_alt' => 0.009,
                    'edad_desde_tasa_alt' => 55,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'observacion' => 'Mínimo 1 año de aporte a Corpentunida. Interés 0.9% para pastores mayores de 54 años de edad.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Las garantías anexas al formulario (pagaré y otros) deben ser firmadas según instrucciones.',
                ],
            ],
            13701017 => [ // Rapi-Crédito Educativo
                'datos' => [
                    'monto_maximo' => 30000000,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 60,
                    'tasa_interes' => 0.004,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'observacion' => 'Cubre matrículas estudiantiles del pastor o su familia. Mínimo 1 año de aporte. Plazo normal 1 año (12 meses); para altas cuantías el plazo máximo sube a 60 meses.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Recibo de pago de matrícula o certificación estudiantil que indique el valor de la matrícula.',
                    'Las garantías anexas al formulario (pagaré y otros) deben ser firmadas según instrucciones.',
                ],
            ],
            13701021 => [ // Rapi-Crédito Vehículo (Convenio Sura)
                'datos' => [
                    'monto_maximo' => null,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 12,
                    'tasa_interes' => 0.01,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'seguro_todo_riesgo' => 1,
                    'observacion' => 'Cubre el costo del seguro todo riesgo vehicular mediante convenio con SURA. Valor máximo = costo de la póliza (variable, no es un monto fijo).',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Las garantías anexas al formulario (pagaré y otros) deben ser firmadas según instrucciones indicadas.',
                    'Carátula de la póliza expedida por SURA, en la que se refleje el costo del seguro.',
                ],
            ],
            13701022 => [ // Rapi-Crédito Salud (Credi-Salud)
                'datos' => [
                    'monto_maximo' => 10000000,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 60,
                    'tasa_interes' => 0.004,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'observacion' => 'Cubre procedimientos médicos, hospitalización, medicamentos y similares para el asociado y su familia. Mínimo 1 año de aporte.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Certificado de diagnóstico médico.',
                    'Las garantías anexas al formulario (pagaré y otros) deben ser firmadas según instrucciones.',
                ],
            ],
            13701010 => [ // Créditos Libre Inversión Menores
                'datos' => [
                    'monto_maximo' => null,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 60,
                    'tasa_interes' => 0.01,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => 54,
                    'observacion' => 'Para asociados menores de 55 años, activos en su ministerio, mínimo 1 año de aporte. Valor máximo: 80% del saldo en el fondo de retiro del asociado (monto variable, no fijo).',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Certificado de Ingresos',
                    'Paz y Salvo Nacional',
                    'Paz y Salvo Distrital',
                    'En caso de que tenga otros ingresos (arriendo, pensión, etc.) debe anexar certificado.',
                ],
            ],
            13701011 => [ // Créditos Libre Inversión Mayores
                'datos' => [
                    'monto_maximo' => null,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 60,
                    'tasa_interes' => 0.009,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => 55,
                    'edad_maxima' => null,
                    'observacion' => 'Para asociados mayores de 55 años, activos en su ministerio, mínimo 1 año de aporte. Valor máximo: 80% del saldo en el fondo de retiro del asociado (monto variable, no fijo).',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Certificado de Ingresos',
                    'Paz y Salvo Nacional',
                    'Paz y Salvo Distrital',
                    'En caso de que tenga otros ingresos (arriendo, pensión, etc.) debe anexar certificado.',
                ],
            ],
            13701016 => [ // Créditos Hipotecarios
                'datos' => [
                    'monto_maximo' => 350000000,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 180,
                    'tasa_interes' => 0.008,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'observacion' => 'Para compra de vivienda nueva o usada. Aprobación definitiva sujeta a seguro de vida que ampare al pastor en caso de muerte. El pastor debe contar con el 10% del valor de compra; Corpentunida presta máximo el 90% del valor del inmueble. Se pueden solicitar montos mayores si el disponible de pago es mínimo el doble de la cuota, con ingresos extra certificados.',
                ],
                'documentos' => [
                    'Carta dirigida a la junta directiva de Corpentunida solicitando autorización para tramitar el crédito hipotecario (requisito previo a la radicación).',
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Paz y Salvo Nacional',
                    'Certificado de Ingresos',
                    'En caso de que tenga otros ingresos (arriendo, pensión, etc.) debe anexar certificado.',
                    'Tras la aprobación: documentos del inmueble (escritura, certificado de tradición, etc.) enviados al área Jurídica para su revisión.',
                ],
            ],
            13701023 => [ // Mi Primera Inversión
                'datos' => [
                    'monto_maximo' => 75000000,
                    'monto_minimo' => null,
                    'plazo_minimo' => null,
                    'plazo_maximo' => 180,
                    'tasa_interes' => 0.004,
                    'tasa_interes_alt' => null,
                    'edad_desde_tasa_alt' => null,
                    'edad_minima' => null,
                    'edad_maxima' => null,
                    'observacion' => 'Para agremiados con ingresos netos desde nivelación y hasta $3.500.000, sin propiedad a su nombre. Permite construcción o compra de primer inmueble. Mínimo 3 años de aporte a Corpentunida. Cuota fija.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Fotocopia o escáner de la cédula del pastor ampliada al 150%',
                    'Fotocopia o escáner de la cédula de la esposa del pastor ampliada al 150%',
                    'Paz y Salvo Nacional',
                    'Paz y Salvo Distrital',
                    'Certificado de Ingresos',
                    'No podrá tener cruces de deudas de seguros, créditos u otros, con el auxilio de retiro, en los últimos 3 años (si los tuvo, debe anexar carta explicando los motivos).',
                    'Si es aprobado, según la inversión a realizar: Certificado de Tradición, Presupuesto de Construcción, Carta de detalle de inversiones, Documento de Propiedad, etc.',
                ],
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->lineas() as $cuenta => $info) {
            $id = DB::table('cre_lineas_creditos')->where('cuenta', $cuenta)->value('id');
            if (!$id) {
                continue; // cuenta no encontrada: no inventamos la línea, se deja para revisión manual
            }

            DB::table('cre_lineas_creditos')->where('id', $id)->update($info['datos']);

            // Idempotente: reemplaza la lista de documentos de esta línea en vez de duplicarla.
            DB::table('cre_lineas_creditos_documentos')->where('cre_lineas_creditos_id', $id)->delete();
            $orden = 0;
            foreach ($info['documentos'] as $descripcion) {
                DB::table('cre_lineas_creditos_documentos')->insert([
                    'cre_lineas_creditos_id' => $id,
                    'descripcion' => $descripcion,
                    'orden' => $orden++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->lineas()) as $cuenta) {
            $id = DB::table('cre_lineas_creditos')->where('cuenta', $cuenta)->value('id');
            if ($id) {
                DB::table('cre_lineas_creditos_documentos')->where('cre_lineas_creditos_id', $id)->delete();
            }
        }
        // No revierte monto_maximo/plazo/tasa a los valores de relleno anteriores a propósito:
        // esos eran datos incorrectos, no algo que valga la pena restaurar.
    }
};
