<?php

namespace App\Http\Controllers\Recaudo;

use App\Http\Controllers\Controller;
use App\Models\Recaudo\RecImputacionContable;
use Illuminate\Http\Request;

class RecImputacionContableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cambiar ->get() por ->paginate(10)
        $imputaciones = RecImputacionContable::with(['transaccion', 'tercero', 'distrito'])
            ->orderBy('id_recibo', 'desc') // Opcional: ordenar por los más recientes
            ->paginate(10); // 10 indica la cantidad de registros por página
        
        return view('recaudo.imputaciones.index', compact('imputaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recaudo.imputaciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Aquí iría tu validación, por ejemplo:
        $validatedData = $request->validate([
            'id_transaccion' => 'required|exists:con_extractos_transacciones,id_transaccion',
            'id_tercero_origen' => 'required|exists:MaeTerceros,cod_ter',
            'id_distrito' => 'required|exists:MaeDistritos,COD_DIST',
            'id_recibo' => 'required|unique:rec_imputaciones_contables,id_recibo',
            'concepto_contable' => 'required|string',
            'valor_imputado' => 'required|integer',
            'link_ecm' => 'nullable|string', // Listo para integrar tu Gestor Documental
            'estado_conciliacion' => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
        ]);

        RecImputacionContable::create($validatedData);

        return redirect()->route('recaudo.imputaciones.index')
                         ->with('success', 'Imputación contable creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RecImputacionContable $recImputacionContable)
    {
        // Cargar relaciones si es necesario para la vista de detalle
        $recImputacionContable->load(['transaccion', 'tercero', 'distrito']);
        
        return view('recaudo.imputaciones.show', compact('recImputacionContable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RecImputacionContable $recImputacionContable)
    {
        return view('recaudo.imputaciones.edit', compact('recImputacionContable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RecImputacionContable $recImputacionContable)
    {
        $validatedData = $request->validate([
            'concepto_contable' => 'required|string',
            'valor_imputado' => 'required|integer',
            'estado_conciliacion' => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
            // Agrega las demás reglas según necesites permitir edición
        ]);

        $recImputacionContable->update($validatedData);

        return redirect()->route('recaudo.imputaciones.index')
                         ->with('success', 'Imputación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RecImputacionContable $recImputacionContable)
    {
        $recImputacionContable->delete();

        return redirect()->route('recaudo.imputaciones.index')
                         ->with('success', 'Imputación eliminada.');
    }
}