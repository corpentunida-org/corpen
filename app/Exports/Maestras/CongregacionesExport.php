<?php

namespace App\Exports\Maestras;

use App\Models\Maestras\MaeCongregacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CongregacionesExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    public function collection()
    {
        return MaeCongregacion::with([
            'maeClaseCongregacion:id,nombre',
            'maeDistritos:COD_DIST,NOM_DIST',
            'maeMunicipios:id,nombre',
            // maeTercero() es hasOne(MaeTerceros, 'congrega', 'codigo') — la columna "congrega"
            // vive en MaeTerceros y es la que Laravel usa para cruzar la relación; si no se
            // incluye aquí, el eager load con columnas limitadas devuelve null en silencio
            // aunque sí exista match (justo lo que pasaba: pastor_actual salía vacío).
            'maeTercero:cod_ter,nom_ter,congrega',
            'pastorAnteriorObj:cod_ter,nom_ter',
        ])
            ->orderBy('nombre')
            ->get()
            ->map(fn ($c) => [
                $c->codigo,
                $c->codigo_texto,
                $c->nombre,
                $c->maeClaseCongregacion->nombre ?? null,
                $c->estado,
                $c->municipio_nombre,
                $c->direccion,
                // Como texto explícito: si no, Excel detecta estos números largos como numéricos
                // y los muestra en notación científica (ej. "3,11E+09" en vez de "3116328671").
                $c->telefono !== null ? (string) $c->telefono : null,
                $c->celular !== null ? (string) $c->celular : null,
                $c->maeDistritos->NOM_DIST ?? null,
                $c->apertura,
                $c->cierre,
                $c->maeTercero->nom_ter ?? null,
                isset($c->maeTercero->cod_ter) ? (string) $c->maeTercero->cod_ter : null,
                $c->pastorAnteriorObj->nom_ter ?? null,
                isset($c->pastorAnteriorObj->cod_ter) ? (string) $c->pastorAnteriorObj->cod_ter : null,
                $c->observacion,
            ]);
    }

    public function headings(): array
    {
        return [
            'codigo', 'codigo_texto', 'nombre', 'clase', 'estado', 'municipio',
            'direccion', 'telefono', 'celular', 'distrito', 'fecha_apertura', 'fecha_cierre',
            'pastor_actual', 'cedula_pastor_actual', 'pastor_anterior', 'cedula_pastor_anterior',
            'observacion',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_TEXT, // telefono
            'I' => NumberFormat::FORMAT_TEXT, // celular
            'N' => NumberFormat::FORMAT_TEXT, // cedula_pastor_actual
            'P' => NumberFormat::FORMAT_TEXT, // cedula_pastor_anterior
        ];
    }
}
