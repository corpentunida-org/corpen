<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Imports\Cartera\ExcelMorososImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class ReadExelController extends Controller
{
    public function index()
    {
        return view("cartera.indexmorosos");
    }

    public function store(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|mimes:xlsx,xls'
        ]);
        
        if ($request->hasFile('documento')) {
            $import = new ExcelMorososImport;
            Excel::import($import, $request->file('documento'));
            $datos = $import->getRows();
            if (!empty($datos)) {
                foreach ($datos as &$row) {
                    foreach ($row as $cellIndex => &$cell) {
                        if ($cellIndex === 8 || $cellIndex === 9) {
                            if (is_numeric($cell)) {
                                $cell = Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($cell - 2)->format('d/m/Y');
                            }
                        }
                    }
                }
                return view('cartera.indexmorosos', ['data' => $datos]);
            }
        }
    }

    public function pdfMora(Request $request)
    {
        $pdf = Pdf::loadView('cartera.morosospdf', [
            'registro' => $request,
            'image_path' => public_path('assets/img/fondoPdf.png'),
        ])
        
        ->setPaper('a4', 'portrait');
        
        return $pdf->download(date('Y-m-d') . " " . $request->CEDULA . " Reporte.pdf");
        //return $pdf->download("Reporte.pdf");
        //return view('cartera.morosospdf', ['registro' => $request, 'image_path' => public_path('assets/img/fondoPdf.jpg')]);
    }
}
