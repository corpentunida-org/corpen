<?php

namespace App\Exports\Maestras;

use App\Models\Maestras\MaeDistritos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DistritosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return MaeDistritos::with([
            'supervisor:cod_ter,nom_ter',
            'primerPresbitero:cod_ter,nom_ter',
            'segundoPresbitero:cod_ter,nom_ter',
            'tercerPresbitero:cod_ter,nom_ter',
            'secretarioPresbiterio:cod_ter,nom_ter',
            'tesoreroPresbiterio:cod_ter,nom_ter',
            'fiscal:cod_ter,nom_ter',
            'asesorCorpen:cod_ter,nom_ter',
        ])
            ->withCount('congregaciones')
            ->orderBy('NOM_DIST')
            ->get()
            ->map(fn ($d) => [
                $d->COD_DIST,
                $d->NOM_DIST,
                $d->DETALLE,
                $d->COMPUEST,
                $d->congregaciones_count,
                $d->supervisor->nom_ter ?? null,
                $d->primerPresbitero->nom_ter ?? null,
                $d->segundoPresbitero->nom_ter ?? null,
                $d->tercerPresbitero->nom_ter ?? null,
                $d->secretarioPresbiterio->nom_ter ?? null,
                $d->tesoreroPresbiterio->nom_ter ?? null,
                $d->fiscal->nom_ter ?? null,
                $d->asesorCorpen->nom_ter ?? null,
            ]);
    }

    public function headings(): array
    {
        return [
            'codigo_distrito', 'nombre_distrito', 'detalle', 'compuesto_por', 'total_congregaciones',
            'supervisor', 'primer_presbitero', 'segundo_presbitero', 'tercer_presbitero',
            'secretario_presbiterio', 'tesorero_presbiterio', 'fiscal', 'asesor_corpen',
        ];
    }
}
