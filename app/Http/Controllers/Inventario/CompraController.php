<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvCompra;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvMetodo;
use App\Models\Inventario\InvReferencia; 
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvBodega;
use App\Models\Inventario\InvMarca;
use App\Models\Maestras\maeTerceros;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    /**
     * Listado de compras
     */
    public function index()
    {
        $compras = InvCompra::with(['metodo', 'usuarioRegistro', 'proveedor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('inventario.compras.index', compact('compras'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $metodos = InvMetodo::all();
        $proveedores = maeTerceros::select('cod_ter', 'nom_ter', 'razon_soc')->get(); 
        
        // Cargamos referencias con sus relaciones para el datalist
        $referencias = InvReferencia::select('id', 'referencia', 'id_InvSubGrupos', 'id_InvBodegas', 'id_InvMarcas')
            ->with(['subgrupo:id,nombre', 'bodega:id,nombre', 'marca:id,nombre']) 
            ->get();    
               
        $subgrupos = InvSubgrupo::select('id', 'nombre')->get();
        $bodegas = InvBodega::select('id', 'nombre')->get();
        $marcas = InvMarca::select('id', 'nombre')->orderBy('nombre')->get(); // <--- Para el modal

        return view('inventario.compras.create', compact('metodos', 'proveedores', 'referencias', 'subgrupos', 'bodegas', 'marcas'));
    }

    /**
     * Guardar compra y generar activos
     */
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
            'detalles.*.invReferencias_id' => 'required|exists:inv_referencias,id',
            'detalles.*.detalle'           => 'nullable|string|max:255',
            'detalles.*.cantidad'          => 'required|numeric|min:0.01',
            'detalles.*.precio'            => 'required|numeric|min:0',
            'costo_iva'         => 'nullable|numeric|min:0',
            'costo_varios'      => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction(); 

            // Gestión de archivo en AWS S3
            $rutaArchivoS3 = null;
            if ($request->hasFile('eg_archivo')) {
                $file = $request->file('eg_archivo');
                $fileName = 'compra_fac_' . $request->numero_factura . '_' . time() . '.' . $file->extension();
                $path = 'corpentunida/inventario/' . $fileName;
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

            // 2. Crear Detalles y Activos
            foreach ($request->detalles as $item) {
                $detalle = InvDetalleCompra::create([
                    'id_InvCompras'     => $compra->id,
                    'invReferencias_id' => $item['invReferencias_id'],
                    'detalle'           => $item['detalle'] ?? null,
                    'cantidades'        => $item['cantidad'],
                    'precio_unitario'   => $item['precio'],
                    'sub_total'         => $item['cantidad'] * $item['precio'],
                ]);

                $cantidadActivos = (int) $item['cantidad'];
                $referencia = InvReferencia::with('subgrupo')->find($detalle->invReferencias_id);

                $nombreSubgrupo = ($referencia && $referencia->subgrupo) 
                                    ? $referencia->subgrupo->nombre 
                                    : 'Sin Subgrupo definido';

                for ($i = 0; $i < $cantidadActivos; $i++) {
                    InvActivo::create([
                        'nombre'                => $nombreSubgrupo, 
                        'unidad_medida'         => '1', 
                        'id_MaeMunicipios'      => 382, 
                        'id_Estado'             => 1,
                        'id_InvDetalleCompras'  => $detalle->id, 
                        'id_usersRegistro'      => auth()->id(), 
                        'invReferencias_id'     => $detalle->invReferencias_id,
                        'fecha_inicio_garantia' => $compra->fecha_factura,
                        // id_InvMarcas ya no se envía aquí, se obtiene vía Referencia
                    ]);
                }
            }

            // 3. IVA y Costos Extra
            $referenciaGenericaId = $request->detalles[0]['invReferencias_id'];
            if ($request->filled('costo_iva') && $request->costo_iva > 0) {
                InvDetalleCompra::create([
                    'id_InvCompras' => $compra->id, 'invReferencias_id' => $referenciaGenericaId,
                    'detalle' => 'IVA Total ($)', 'cantidades' => 0,
                    'precio_unitario' => $request->costo_iva, 'sub_total' => 0,
                ]);
            }
            if ($request->filled('costo_varios') && $request->costo_varios > 0) {
                InvDetalleCompra::create([
                    'id_InvCompras' => $compra->id, 'invReferencias_id' => $referenciaGenericaId,
                    'detalle' => 'Otros Costos / Varios ($)', 'cantidades' => 0,
                    'precio_unitario' => $request->costo_varios, 'sub_total' => 0,
                ]);
            }

            DB::commit(); 
            return redirect()->route('inventario.compras.index')->with('success', 'Compra guardada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack(); 
            if (isset($rutaArchivoS3)) Storage::disk('s3')->delete($rutaArchivoS3);
            return redirect()->back()->withInput()->with('error', 'Error en el proceso: ' . $e->getMessage());
        }
    }

    /**
     * Crear Referencia vía AJAX desde el modal
     */
    public function storeReferenciaAjax(Request $request)
    {
        try {
            // Mapeamos y validamos los nombres de los campos que envía tu JS
            $request->validate([
                'referencia'     => 'required|string|max:255|unique:inv_referencias,referencia',
                'detalle'        => 'nullable|string|max:255',
                'id_MaeSubgrupo' => 'required|exists:inv_subgrupos,id',
                'id_InvBodegas'  => 'required|exists:inv_bodegas,id', 
                'id_MaeMarcas'   => 'required|exists:inv_marcas,id', 
            ]);

            // Guardamos usando los campos correctos de la Base de Datos
            $referencia = InvReferencia::create([
                'referencia'      => $request->referencia,
                'detalle'         => $request->detalle,
                'id_InvSubGrupos' => $request->id_MaeSubgrupo, // Traducido
                'id_InvBodegas'   => $request->id_InvBodegas,
                'id_InvMarcas'    => $request->id_MaeMarcas,   // Traducido
            ]);

            return response()->json([
                'success'    => true,
                'referencia' => $referencia
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura errores de validación (ej. Nombre duplicado) y los envía al frontend
            $errores = implode(' ', $e->validator->errors()->all());
            return response()->json(['success' => false, 'message' => $errores]);
        } catch (\Exception $e) {
            // Captura cualquier otro error interno
            return response()->json(['success' => false, 'message' => 'Error de servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar Referencia vía AJAX desde el modal (NUEVO MÉTODO)
     */
    public function updateReferenciaAjax(Request $request, $id)
    {
        try {
            // Buscamos la referencia a actualizar
            $referencia = InvReferencia::findOrFail($id);

            // Validamos ignorando el unique del ID actual
            $request->validate([
                'referencia'     => 'required|string|max:255|unique:inv_referencias,referencia,' . $id,
                'detalle'        => 'nullable|string|max:255',
                'id_MaeSubgrupo' => 'required|exists:inv_subgrupos,id',
                'id_InvBodegas'  => 'required|exists:inv_bodegas,id', 
                'id_MaeMarcas'   => 'required|exists:inv_marcas,id', 
            ]);

            // Actualizamos la base de datos
            $referencia->update([
                'referencia'      => $request->referencia,
                'detalle'         => $request->detalle,
                'id_InvSubGrupos' => $request->id_MaeSubgrupo, // Traducido
                'id_InvBodegas'   => $request->id_InvBodegas,
                'id_InvMarcas'    => $request->id_MaeMarcas,   // Traducido
            ]);

            return response()->json([
                'success'    => true,
                'referencia' => $referencia
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errores = implode(' ', $e->validator->errors()->all());
            return response()->json(['success' => false, 'message' => $errores]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error de servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver detalle de compra
     */
    public function show($id)
    {
        $compra = InvCompra::with([
            'detalles' => function($query) { $query->withTrashed(); },
            'detalles.referencia.subgrupo',
            'detalles.referencia.marca', // <--- Incluimos marca
            'proveedor', 
            'metodo', 
            'usuarioRegistro'
        ])->findOrFail($id);

        return view('inventario.compras.show', compact('compra'));
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $compra = InvCompra::with([
            'detalles' => function($q) { $q->withTrashed(); },
            'detalles.referencia.subgrupo',
            'proveedor'
        ])->findOrFail($id);
        
        $metodos = InvMetodo::all();
        $proveedores = maeTerceros::select('cod_ter', 'nom_ter')->get(); 
        $referencias = InvReferencia::with(['subgrupo', 'marca'])->get();
        $subgrupos = InvSubgrupo::select('id', 'nombre')->get();
        $bodegas = InvBodega::select('id', 'nombre')->get();
        $marcas = InvMarca::select('id', 'nombre')->orderBy('nombre')->get(); // <--- También necesario aquí

        $extraCosts = [
            'iva' => $compra->detalles->where('cantidades', 0)
                        ->filter(fn($i) => str_contains(strtolower($i->detalle), 'iva'))
                        ->first()->precio_unitario ?? 0,
            'varios' => $compra->detalles->where('cantidades', 0)
                        ->filter(fn($i) => str_contains(strtolower($i->detalle), 'otros') || str_contains(strtolower($i->detalle), 'varios'))
                        ->first()->precio_unitario ?? 0,
        ];

        return view('inventario.compras.edit', compact('compra', 'metodos', 'proveedores', 'referencias', 'extraCosts', 'subgrupos', 'bodegas', 'marcas'));
    }

    /**
     * Actualizar compra
     */
    public function update(Request $request, $id)
    {
        $compra = InvCompra::findOrFail($id);

        $request->validate([
            'cod_ter_proveedor' => 'required|exists:MaeTerceros,cod_ter',
            'numero_factura'    => 'required|unique:inv_compras,numero_factura,' . $id,
            'fecha_factura'     => 'required|date',
            'id_InvMetodos'     => 'required|exists:inv_metodos,id',
            'total_pago'        => 'required|numeric|min:0',
            'eg_archivo'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'detalles'          => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('eg_archivo')) {
                if ($compra->eg_archivo) {
                    Storage::disk('s3')->delete($compra->eg_archivo);
                }
                $file = $request->file('eg_archivo');
                $path = $file->storeAs('corpentunida/inventario', 'fac_'.$request->numero_factura.'_'.time().'.'.$file->extension(), 's3');
                $compra->eg_archivo = $path;
            }

            $compra->update([
                'cod_ter_proveedor' => $request->cod_ter_proveedor,
                'numero_factura'    => $request->numero_factura,
                'fecha_factura'     => $request->fecha_factura,
                'num_doc_interno'   => $request->num_doc_interno,
                'numero_egreso'     => $request->numero_egreso,
                'id_InvMetodos'     => $request->id_InvMetodos,
                'total_pago'        => $request->total_pago,
            ]);

            if ($request->has('detalles_eliminados')) {
                InvDetalleCompra::whereIn('id', $request->detalles_eliminados)->delete();
            }

            foreach ($request->detalles as $item) {
                $datosDetalle = [
                    'invReferencias_id' => $item['invReferencias_id'],
                    'detalle'           => $item['detalle'],
                    'cantidades'        => $item['cantidad'],
                    'precio_unitario'   => $item['precio'],
                    'sub_total'         => $item['cantidad'] * $item['precio'],
                ];

                if (isset($item['id']) && !empty($item['id'])) {
                    InvDetalleCompra::where('id', $item['id'])->update($datosDetalle);
                } else {
                    $compra->detalles()->create($datosDetalle);
                }
            }

            // Actualizar cargos extra
            InvDetalleCompra::where('id_InvCompras', $compra->id)->where('cantidades', 0)->forceDelete();
            $anchorRef = $request->detalles[0]['invReferencias_id'];

            if ($request->costo_iva > 0) {
                $compra->detalles()->create([
                    'invReferencias_id' => $anchorRef,
                    'detalle' => 'IVA Total ($)', 'cantidades' => 0,
                    'precio_unitario' => $request->costo_iva, 'sub_total' => 0
                ]);
            }
            if ($request->costo_varios > 0) {
                $compra->detalles()->create([
                    'invReferencias_id' => $anchorRef,
                    'detalle' => 'Otros Costos / Varios ($)', 'cantidades' => 0,
                    'precio_unitario' => $request->costo_varios, 'sub_total' => 0
                ]);
            }

            DB::commit();
            return redirect()->route('inventario.compras.show', $compra->id)->with('success', 'Compra actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar compra
     */
    public function destroy($id)
    {
        try {
            $compra = InvCompra::findOrFail($id);
            if ($compra->eg_archivo) {
                Storage::disk('s3')->delete($compra->eg_archivo);
            }
            $compra->detalles()->delete();
            $compra->delete();

            return redirect()->route('inventario.compras.index')->with('success', 'Compra eliminada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Utilidades: S3 y PDF
     */
    public function verArchivoS3($id)
    {
        $compra = InvCompra::findOrFail($id);
        if (!$compra->eg_archivo) return back()->with('error', 'No hay archivo adjunto.');

        if (Storage::disk('s3')->exists($compra->eg_archivo)) {
            $url = Storage::disk('s3')->temporaryUrl($compra->eg_archivo, now()->addMinutes(10));
            return redirect()->away($url); 
        }
        return back()->with('error', 'Archivo no encontrado en S3.');
    }

    public function descargarFactura($id)
    {
        $compra = InvCompra::with(['detalles.referencia', 'proveedor', 'metodo', 'usuarioRegistro'])->findOrFail($id);
        $pdf = Pdf::loadView('inventario.compras.pdf', compact('compra'));
        return $pdf->download('Factura_' . $compra->numero_factura . '.pdf');
    }
}