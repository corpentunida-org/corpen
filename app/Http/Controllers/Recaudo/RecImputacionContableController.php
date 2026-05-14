<?php

namespace App\Http\Controllers\Recaudo;

use App\Http\Controllers\Controller;
use App\Models\Recaudo\RecImputacionContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecImputacionContableController extends Controller
{
    /**
     * Listado con filtros dinámicos y soporte para fragmentos AJAX.
     */
    public function index(Request $request)
    {
        // Iniciamos la consulta con Eager Loading para evitar el problema N+1
        $query = RecImputacionContable::with(['transaccion', 'tercero', 'distrito']);

        // 1. Filtro de búsqueda (Live Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_recibo', 'like', "%$search%")
                  ->orWhere('concepto_contable', 'like', "%$search%")
                  ->orWhere('id_tercero_origen', 'like', "%$search%");
            });
        }

        // 2. Filtro por Estado
        if ($request->filled('estado_conciliacion')) {
            $query->where('estado_conciliacion', $request->estado_conciliacion);
        }

        // 3. Orden y Paginación
        $imputaciones = $query->orderBy('id_recibo', 'desc')->paginate(10);

        // 4. Soporte para AJAX (Fragmento de la tabla para refresco rápido)
        if ($request->ajax()) {
            return view('recaudo.imputaciones.index', compact('imputaciones'))
                ->fragment('imputaciones-list');
        }

        return view('recaudo.imputaciones.index', compact('imputaciones'));
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        return view('recaudo.imputaciones.create');
    }

    /**
     * Almacena una nueva imputación con validación estricta contra maestras.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_transaccion'      => 'required|exists:con_extractos_transacciones,id_transaccion',
            'id_tercero_origen'   => 'required|exists:MaeTerceros,cod_ter',
            'id_distrito'         => 'required|exists:MaeDistritos,COD_DIST',
            'id_recibo'           => 'required|unique:rec_imputaciones_contables,id_recibo',
            'concepto_contable'   => 'required|string|max:1000',
            'valor_imputado'      => 'required|numeric|min:0',
            'link_ecm'            => 'nullable|url',
            'estado_conciliacion' => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
        ]);

        try {
            DB::beginTransaction();
            
            RecImputacionContable::create($validatedData);
            
            DB::commit();

            return redirect()->route('recaudo.imputaciones.index')
                ->with('success', 'Imputación contable creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al procesar el registro: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el detalle completo (Ficha de Auditoría).
     */
    public function show(RecImputacionContable $recImputacionContable)
    {
        // Cargamos relaciones anidadas (Extracto -> Cuenta Bancaria)
        $recImputacionContable->load(['transaccion.cuentaBancaria', 'tercero', 'distrito']);
        
        return view('recaudo.imputaciones.show', compact('recImputacionContable'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(RecImputacionContable $recImputacionContable)
    {
        return view('recaudo.imputaciones.edit', compact('recImputacionContable'));
    }

    /**
     * Actualiza el registro.
     */
    public function update(Request $request, RecImputacionContable $recImputacionContable)
    {
        $validatedData = $request->validate([
            'concepto_contable'   => 'required|string|max:1000',
            'valor_imputado'      => 'required|numeric|min:0',
            'estado_conciliacion' => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
            'link_ecm'            => 'nullable|url',
        ]);

        try {
            $recImputacionContable->update($validatedData);

            return redirect()->route('recaudo.imputaciones.index')
                ->with('success', 'Imputación actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'No se pudo actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina el registro (Considera usar SoftDeletes en el Modelo por auditoría).
     */
    public function destroy(RecImputacionContable $recImputacionContable)
    {
        try {
            $recImputacionContable->delete();

            return redirect()->route('recaudo.imputaciones.index')
                ->with('success', 'Imputación eliminada del sistema.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el registro.']);
        }
    }
}