<x-base-layout>
    <div class="container">
        <h1 class="title">Detalle de Tarea</h1>

        <div class="card p-4 mb-4">
            <p><strong>Título:</strong> {{ $task->titulo }}</p>
            <p><strong>Descripción:</strong> {{ $task->descripcion }}</p>
            <p><strong>Workflow:</strong> {{ $task->workflow->nombre ?? 'Sin Workflow' }}</p>
            <p><strong>Responsable:</strong> {{ $task->user->name ?? 'Sin asignar' }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($task->estado) }}</p>
            <p><strong>Prioridad:</strong> {{ ucfirst($task->prioridad) }}</p>
            <p><strong>Creado:</strong> {{ $task->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Actualizado:</strong> {{ $task->updated_at->format('d/m/Y H:i') }}</p>
        </div>

        <a href="{{ route('flujo.tablero') }}" class="btn-clear">Volver al Tablero</a>
    </div>
</x-base-layout>
