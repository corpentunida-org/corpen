<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvCompra;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvMetodo;
use App\Models\Inventario\InvReferencia; 
use App\Models\Maestras\maeTerceros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    public function index()
    {
        // Cargamos la compra junto con su proveedor, método de pago y usuario
        $compras = InvCompra::with(['metodo', 'usuarioRegistro', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('inventario.compras.index', compact('compras'));
    }

    public function create()
    {
        $metodos = InvMetodo::all();
        $proveedores = maeTerceros::select('cod_ter', 'nom_ter', 'razon_soc')->get(); 
        
        // Cargamos las referencias para enviarlas al formulario
        $referencias = InvReferencia::select('id', 'nombre')->get(); 
        
        return view('inventario.compras.create', compact('metodos', 'proveedores', 'referencias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cod_ter_proveedor' => 'required|exists:MaeTerceros,cod_ter',
            'numero_factura'    => 'required|unique:inv_compras,numero_factura',
            'fecha_factura'     => 'required|date',
            'total_pago'        => 'required|numeric|min:0',
            'id_InvMetodos'     => 'required|exists:inv_metodos,id',
            'numero_egreso'     => 'nullable|integer',
            'eg_archivo'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', 
            'detalles'          => 'required|array|min:1',
            
            // Validamos los campos del detalle
            'detalles.*.invReferencias_id' => 'required|exists:inv_referencias,id',
            'detalles.*.detalle'           => 'nullable|string|max:255',
            'detalles.*.cantidad'          => 'required|numeric|min:0.01',
            'detalles.*.precio'            => 'required|numeric|min:0',

            // Validamos que los costos extra vengan correctamente
            'costo_iva'         => 'nullable|numeric|min:0',
            'costo_varios'      => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction(); 

            // --- LÓGICA PARA SUBIR A AWS S3 CON GET_CONTENTS ---
            $rutaArchivoS3 = null;
            if ($request->hasFile('eg_archivo')) {
                $file = $request->file('eg_archivo');
                // Nombramos el archivo usando la factura y la hora
                $fileName = 'compra_fac_' . $request->numero_factura . '_' . time() . '.' . $file->extension();
                $path = 'corpentunida/inventario/' . $fileName;

                // Subimos a S3
                Storage::disk('s3')->put($path, file_get_contents($file));
                $rutaArchivoS3 = $path;
            }

            // 1. Crear Cabecera
            $compra = InvCompra::create([
                'cod_ter_proveedor' => $request->cod_ter_proveedor,
                'numero_factura'    => $request->numero_factura,
                'fecha_factura'     => $request->fecha_factura,
                'total_pago'        => $request->total_pago,
                'num_doc_interno'   => $request->num_doc_interno,
                'numero_egreso'     => $request->numero_egreso,
                'eg_archivo'        => $rutaArchivoS3,
                'id_InvMetodos'     => $request->id_InvMetodos,
                'id_usersRegistro'  => auth()->id(),
            ]);

            // 2. Crear Detalles (Productos regulares)
            foreach ($request->detalles as $item) {
                InvDetalleCompra::create([
                    'id_InvCompras'     => $compra->id,
                    'invReferencias_id' => $item['invReferencias_id'],
                    'detalle'           => $item['detalle'] ?? null,
                    'cantidades'        => $item['cantidad'],
                    'precio_unitario'   => $item['precio'],
                    'sub_total'         => $item['cantidad'] * $item['precio'],
                ]);
            }

            // ==========================================
            // 3. NUEVO: INSERTAR IVA Y OTROS COSTOS
            // ==========================================
            
            // Tomamos el ID del primer producto para usarlo en los registros extra 
            // y no romper la llave foránea 'invReferencias_id'.
            $referenciaGenericaId = $request->detalles[0]['invReferencias_id'];

            // Guardar IVA si existe y es mayor a 0
            if ($request->filled('costo_iva') && $request->costo_iva > 0) {
                InvDetalleCompra::create([
                    'id_InvCompras'     => $compra->id,
                    'invReferencias_id' => $referenciaGenericaId,
                    'detalle'           => 'IVA Total ($)',
                    'cantidades'        => 0,
                    'precio_unitario'   => $request->costo_iva,
                    'sub_total'         => 0,
                ]);
            }

            // Guardar Otros Costos si existe y es mayor a 0
            if ($request->filled('costo_varios') && $request->costo_varios > 0) {
                InvDetalleCompra::create([
                    'id_InvCompras'     => $compra->id,
                    'invReferencias_id' => $referenciaGenericaId,
                    'detalle'           => 'Otros Costos / Varios ($)',
                    'cantidades'        => 0,
                    'precio_unitario'   => $request->costo_varios,
                    'sub_total'         => 0,
                ]);
            }

            DB::commit(); 
            return redirect()
                ->route('inventario.compras.index')
                ->with('success', 'Compra de factura ' . $request->numero_factura . ' registrada con éxito.');

        } catch (\Exception $e) {
            DB::rollBack(); 
            
            // Si hubo error en BD pero el archivo subió, lo eliminamos
            if (isset($rutaArchivoS3)) {
                Storage::disk('s3')->delete($rutaArchivoS3);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al insertar en DB: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Cargamos 'detalles.referencia' para poder mostrar el nombre del producto en la vista
        $compra = InvCompra::with(['detalles.referencia', 'proveedor', 'metodo', 'usuarioRegistro'])->findOrFail($id);
        
        return view('inventario.compras.show', compact('compra'));
    }

    public function verArchivoS3($id)
    {
        $compra = InvCompra::findOrFail($id);

        if (!$compra->eg_archivo) {
            return back()->with('error', 'Esta compra no tiene archivo adjunto.');
        }

        $url = '#';
        if (Storage::disk('s3')->exists($compra->eg_archivo)) {
            $url = Storage::disk('s3')->temporaryUrl(
                $compra->eg_archivo, now()->addMinutes(5)
            );
            return redirect()->away($url); 
        }

        return back()->with('error', 'El archivo no se encontró en el servidor AWS.');
    }

    public function descargarFactura($id)
    {
        // Cargamos 'detalles.referencia' también para el PDF
        $compra = InvCompra::with(['detalles.referencia', 'proveedor', 'metodo', 'usuarioRegistro'])->findOrFail($id);
        
        $pdf = Pdf::loadView('inventario.compras.pdf', compact('compra'));
        
        return $pdf->download('Factura_' . $compra->numero_factura . '.pdf');
    }

    public function storeReferenciaAjax(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:255|unique:inv_referencias,nombre',
            'detalle' => 'nullable|string|max:255',
        ]);

        $referencia = InvReferencia::create([
            'nombre'  => $request->nombre,
            'detalle' => $request->detalle,
        ]);

        return response()->json([
            'success'    => true,
            'referencia' => $referencia
        ]);
    }
}