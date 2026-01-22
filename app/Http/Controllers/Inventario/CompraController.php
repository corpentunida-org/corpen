<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvCompra;
use App\Models\Inventario\InvDetalleCompra;
use App\Models\Inventario\InvMetodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Vital para transacciones

class CompraController extends Controller
{
    public function index()
    {
        $compras = InvCompra::with('metodo', 'usuarioRegistro')->orderBy('created_at', 'desc')->paginate(10);
        return view('inventario.compras.index', compact('compras'));
    }

    public function create()
    {
        $metodos = InvMetodo::all();
        return view('inventario.compras.create', compact('metodos'));
    }

    public function store(Request $request)
    {
        // Validación Cabecera y Detalle (Array)
        $request->validate([
            'numero_factura' => 'required|unique:inv_compras',
            'fecha_factura' => 'required|date',
            'detalles' => 'required|array|min:1' // Debe venir al menos un producto
        ]);

        try {
            DB::beginTransaction(); // Inicia bloque seguro

            // 1. Crear Cabecera
            $compra = InvCompra::create([
                'numero_factura' => $request->numero_factura,
                'fecha_factura' => $request->fecha_factura,
                'total_pago' => $request->total_pago,
                'num_doc_interno' => $request->num_doc_interno,
                'id_InvMetodos' => $request->id_InvMetodos,
                'id_usersRegistro' => auth()->id(),
            ]);

            // 2. Crear Detalles
            foreach ($request->detalles as $item) {
                InvDetalleCompra::create([
                    'id_InvCompras' => $compra->id,
                    'cantidades' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'sub_total' => $item['cantidad'] * $item['precio'],
                ]);
            }

            DB::commit(); // Guarda todo si no hubo errores
            return redirect()->route('compras.index')->with('success', 'Compra registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack(); // Deshace todo si algo falló
            return back()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $compra = InvCompra::with('detalles')->findOrFail($id);
        return view('inventario.compras.show', compact('compra'));
    }
}