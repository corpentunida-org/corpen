<?php

namespace App\Http\Controllers\Flujo;

use App\Http\Controllers\Controller;
use App\Models\Flujo\Workflow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkflowController extends Controller
{
    /**
     * Listado de workflows con búsqueda y filtros mejorados.
     */
    public function index(Request $request)
    {
        // Incluimos la relación 'asignado' para evitar el problema N+1
        $query = Workflow::with(['creator', 'modifier', 'asignado']);

        // Búsqueda por nombre o descripción
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        // Filtros dinámicos
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('creado_por')) {
            $query->where('creado_por', $request->creado_por);
        }
        if ($request->filled('asignado_a')) {
            $query->where('asignado_a', $request->asignado_a);
        }

        $workflows = $query->latest()->paginate(10);

        // Datos para los filtros y selects
        $users = User::select('id', 'name')->orderBy('name')->get();
        $estados = $this->getEstadosOptions();
        $prioridades = $this->getPrioridadesOptions();

        return view('flujo.workflows.index', compact('workflows', 'users', 'estados', 'prioridades'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        $estados = ['borrador' => 'Borrador', 'activo' => 'Activo'];
        $prioridades = $this->getPrioridadesOptions();

        return view('flujo.workflows.create', compact('users', 'estados', 'prioridades'));
    }

    /**
     * Guardar un nuevo workflow.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string|max:1000',
            'estado'        => ['required', Rule::in(array_keys($this->getEstadosOptions()))],
            'prioridad'     => ['required', Rule::in(array_keys($this->getPrioridadesOptions()))],
            'fecha_inicio'  => 'nullable|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',
            'creado_por'    => 'required|exists:users,id',
            'asignado_a'    => 'nullable|exists:users,id', // Validación para el nuevo campo
            'configuracion' => 'nullable', 
        ]);

        // Procesar JSON si viene del frontend como string
        if ($request->filled('configuracion') && is_string($request->configuracion)) {
            $data['configuracion'] = json_decode($request->configuracion, true);
        }

        $data['activo'] = $request->has('activo');
        $data['es_plantilla'] = $request->has('es_plantilla');

        Workflow::create($data);

        return redirect()->route('flujo.workflows.index')
            ->with('success', '✅ Proceso creado y asignado correctamente.');
    }

    /**
     * Detalle del workflow.
     */
    public function show(Workflow $workflow)
    {
        $workflow->load(['creator', 'modifier', 'asignado', 'tasks' => function ($query) {
            $query->latest();
        }]);

        return view('flujo.workflows.show', compact('workflow'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Workflow $workflow)
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        $estados = $this->getEstadosOptions();
        $prioridades = $this->getPrioridadesOptions();
        
        $workflow->load(['creator', 'modifier', 'asignado']);

        return view('flujo.workflows.edit', compact('workflow', 'users', 'estados', 'prioridades'));
    }

    /**
     * Actualizar workflow.
     */
    public function update(Request $request, Workflow $workflow)
    {
        try {
            DB::beginTransaction();
            
            // Validar el campo JSON primero
            $configuracion = $this->procesarConfiguracionJson($request);
            
            $data = $request->validate([
                'nombre'        => 'required|string|max:255',
                'descripcion'   => 'nullable|string|max:1000',
                'estado'        => ['required', Rule::in(array_keys($this->getEstadosOptions()))],
                'prioridad'     => ['required', Rule::in(array_keys($this->getPrioridadesOptions()))],
                'fecha_inicio'  => 'nullable|date',
                'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',
                'creado_por'    => 'required|exists:users,id',
                'asignado_a'    => 'nullable|exists:users,id', // Campo actualizado
            ], [
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
                'asignado_a.exists' => 'El usuario asignado no es válido.'
            ]);
            
            $data['configuracion'] = $configuracion;
            $data['activo'] = $request->has('activo');
            $data['es_plantilla'] = $request->has('es_plantilla');
            $data['modificado_por'] = auth()->id();
            
            $workflow->update($data);
            
            DB::commit();
            
            return redirect()->route('flujo.workflows.index')
                ->with('success', '✏️ Proyecto actualizado y reasignado correctamente.');
                
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar workflow: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['general' => 'Error crítico al procesar la actualización.']);
        }
    }

    /**
     * Procesa y valida el campo JSON.
     */
    private function procesarConfiguracionJson(Request $request)
    {
        $configuracionJson = $request->input('configuracion');
        
        if (empty($configuracionJson) || $configuracionJson === '[]' || $configuracionJson === '{}') {
            return null;
        }

        // Si ya es un array (por alguna validación previa o cast), lo devolvemos
        if (is_array($configuracionJson)) return $configuracionJson;
        
        $decodedConfig = json_decode($configuracionJson, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                'configuracion' => 'El formato JSON es inválido.'
            ]);
        }
        
        return $decodedConfig;
    }

    private function getEstadosOptions()
    {
        return [
            'borrador' => 'Borrador',
            'activo' => 'Activo',
            'pausado' => 'Pausado',
            'completado' => 'Completado',
            'archivado' => 'Archivado'
        ];
    }

    private function getPrioridadesOptions()
    {
        return [
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'crítica' => 'Crítica'
        ];
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('flujo.workflows.index')
            ->with('success', '🗑️ Proceso eliminado correctamente.');
    }
}