<?php

namespace App\Http\Controllers\Cartera;

use App\Http\Controllers\Controller;
use App\Imports\Cartera\ExcelMorososImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class ReadExelController extends Controller
{
    public function index(){
        return view("cartera.indexmorosos");
    }

    public function store(Request $request){
        $request->validate([
            'documento' => 'required|file|mimes:xlsx,xls'
        ]);
        if($request->hasFile('documento')){
            $import = new ExcelMorososImport;
            Excel::import($import, $request->file('documento'));
            $datos = $import->getRows();
            if(!empty($datos)){
                return view('cartera.indexmorosos', ['data' => $datos]);
            }
        }
        return redirect()->back()->with('error', 'No se pudo procesar el archivo.');
    }

    public function pdfMora(Request $request){
        $pdf = Pdf::loadView('cartera.morosospdf', 
            ['registro' => $request,'image_path' => public_path('assets/img/fondoPdf.jpg'),]);
        return $pdf->download(date('Y-m-d') . " " . $request->CEDULA . " Reporte.pdf");
        //return view('cartera.morosospdf', ['registro' => $request]);
    }
}
