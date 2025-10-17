<x-base-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="fw-bold text-primary mb-4">
                <i class="feather-eye me-2"></i> Detalle del Canal
            </h5>

            <dl class="row small">
                <dt class="col-sm-3">ID:</dt>
                <dd class="col-sm-9">#{{ $channel->id }}</dd>

                <dt class="col-sm-3">Nombre:</dt>
                <dd class="col-sm-9">{{ $channel->name }}</dd>
            </dl>

            <div class="mt-4">
                <a href="{{ route('interactions.channels.index') }}" class="btn btn-secondary me-2">
                    <i class="feather-arrow-left me-1"></i> Volver
                </a>
                <a href="{{ route('interactions.channels.edit', $channel->id) }}" class="btn btn-primary">
                    <i class="feather-edit-3 me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>
</x-base-layout>
