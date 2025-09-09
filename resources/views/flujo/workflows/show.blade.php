<x-base-layout>
    <div class="container">
        <h1>Detalle del Workflow</h1>

        <p><strong>ID:</strong> {{ $workflow->id }}</p>
        <p><strong>Nombre:</strong> {{ $workflow->nombre }}</p>
        <p><strong>Descripción:</strong> {{ $workflow->descripcion }}</p>
        <p><strong>Creador:</strong> {{ $workflow->creator?->name }}</p>

        <h2>Tareas relacionadas</h2>
        <ul>
            @forelse($workflow->tasks as $task)
                <li>{{ $task->titulo }} (Estado: {{ $task->estado }})</li>
            @empty
                <li>No hay tareas asociadas a este workflow.</li>
            @endforelse
        </ul>

        <a href="{{ route('flujo.workflows.index') }}">⬅ Volver al listado</a>
    </div>
</x-base-layout>
