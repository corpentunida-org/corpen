<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ampliación de Hipotecarios y Retanqueo Hipotecario: dos operaciones sobre un crédito
 * hipotecario ya existente, no líneas de crédito "nuevas" independientes. El usuario confirmó
 * que comparten la misma cuenta contable que Créditos Hipotecarios (13701016) y que el tipo de
 * cuota puede ser fija o variable según el caso — como cre_tipos_creditos_id es un FK NOT NULL
 * de un solo valor, no puede representar "cualquiera de las dos" directamente, así que se deja
 * en "Cuota Fija" (mismo valor que la línea base de Hipotecarios) y se aclara en observacion.
 * Misma garantía (1) que Créditos Hipotecarios, por ser operaciones sobre el mismo inmueble
 * hipotecado.
 */
return new class extends Migration
{
    private function lineas(): array
    {
        return [
            'Ampliación de Hipotecarios' => [
                'datos' => [
                    'cuenta' => 13701016,
                    'cre_tipos_creditos_id' => 1,
                    'cre_garantias_id' => 1,
                    'monto_maximo' => null,
                    'monto_minimo' => null,
                    'plazo_maximo' => 180,
                    'tasa_interes' => 0.0080,
                    'observacion' => 'Modificación del préstamo hipotecario actual para aumentar el capital prestado (reformas, liquidez, ampliar plazo). Sujeto a aprobación de junta directiva; hasta 90% del valor actual de la garantía (inmueble hipotecado), según capacidad de pago e ingresos extra certificados. Tipo de cuota (fija o variable) se define caso a caso.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Fotocopia o escáner de la cédula del pastor',
                    'Fotocopia o escáner de la cédula de la esposa del pastor',
                    'Certificado de Ingresos, expedido por la Secretaría Nacional de la IPUC',
                    'Certificado de Tradición del Inmueble con fecha reciente de expedición.',
                    'En caso de que tenga otros ingresos (arriendo, pensión, salario esposa, etc.) debe anexar certificado.',
                ],
            ],
            'Retanqueo Hipotecario' => [
                'datos' => [
                    'cuenta' => 13701016,
                    'cre_tipos_creditos_id' => 1,
                    'cre_garantias_id' => 1,
                    'monto_maximo' => null,
                    'monto_minimo' => null,
                    'plazo_maximo' => 180,
                    'tasa_interes' => 0.0080,
                    'observacion' => 'Volver a dejar el crédito hipotecario en su valor inicial aprobado. Sujeto a aprobación de junta directiva; hasta 90% del valor actual de la garantía (inmueble hipotecado), según capacidad de pago e ingresos extra certificados. Tipo de cuota (fija o variable) se define caso a caso.',
                ],
                'documentos' => [
                    'Formulario diligenciado y firmado por el pastor y su esposa',
                    'Certificado de Ingresos, expedido por la Secretaría Nacional de la IPUC',
                    'Certificado de Tradición del Inmueble con fecha reciente de expedición.',
                    'En caso de que tenga otros ingresos (arriendo, pensión, salario esposa, etc.) debe anexar certificado.',
                ],
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->lineas() as $nombre => $info) {
            $id = DB::table('cre_lineas_creditos')->where('nombre', $nombre)->value('id');

            if (!$id) {
                $id = DB::table('cre_lineas_creditos')->insertGetId(array_merge($info['datos'], [
                    'nombre' => $nombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            } else {
                DB::table('cre_lineas_creditos')->where('id', $id)->update($info['datos']);
            }

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
        foreach (array_keys($this->lineas()) as $nombre) {
            $id = DB::table('cre_lineas_creditos')->where('nombre', $nombre)->value('id');
            if ($id) {
                DB::table('cre_lineas_creditos_documentos')->where('cre_lineas_creditos_id', $id)->delete();
                DB::table('cre_lineas_creditos')->where('id', $id)->delete();
            }
        }
    }
};
