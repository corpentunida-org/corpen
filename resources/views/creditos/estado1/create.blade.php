<x-base-layout>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Crear Nueva Fábrica</h5>
                <a href="{{ route('estado1.index') }}" class="btn btn-sm btn-secondary">← Volver a la lista</a>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>¡Ups! Hay algunos errores:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('estado1.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Producto Financiero</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="cuenta" class="form-label">Cuenta</label>
                        <input type="text" name="cuenta" id="cuenta" class="form-control" value="{{ old('cuenta') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <input type="text" name="tipo" id="tipo" class="form-control" value="{{ old('tipo') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="tasa_interes" class="form-label">Tasa de Interés (%)</label>
                        <input type="number" step="0.01" name="tasa_interes" id="tasa_interes" class="form-control" value="{{ old('tasa_interes') }}" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="{{ route('estado1.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
