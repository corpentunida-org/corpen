<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4 px-2">Editar Fábrica</h5>

            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ups!</strong> Hay algunos problemas con tus datos.<br><br>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('estado1.update', $fabrica->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 px-2">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $fabrica->nombre) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="cuenta" class="form-label">Cuenta</label>
                        <input type="text" name="cuenta" class="form-control" value="{{ old('cuenta', $fabrica->cuenta) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="tipo" class="form-label">Tipo</label>
                        <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $fabrica->tipo) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="tasa_interes" class="form-label">Tasa de Interés (%)</label>
                        <input type="number" step="0.01" name="tasa_interes" class="form-control" value="{{ old('tasa_interes', $fabrica->tasa_interes) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="plazo_minimo" class="form-label">Plazo Mínimo</label>
                        <input type="number" name="plazo_minimo" class="form-control" value="{{ old('plazo_minimo', $fabrica->plazo_minimo) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="plazo_maximo" class="form-label">Plazo Máximo</label>
                        <input type="number" name="plazo_maximo" class="form-control" value="{{ old('plazo_maximo', $fabrica->plazo_maximo) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="edad_minima" class="form-label">Edad Mínima</label>
                        <input type="number" name="edad_minima" class="form-control" value="{{ old('edad_minima', $fabrica->edad_minima) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="edad_maxima" class="form-label">Edad Máxima</label>
                        <input type="number" name="edad_maxima" class="form-control" value="{{ old('edad_maxima', $fabrica->edad_maxima) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_apertura" class="form-label">Fecha de Apertura</label>
                        <input type="date" name="fecha_apertura" class="form-control" value="{{ old('fecha_apertura', $fabrica->fecha_apertura) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="fecha_cierre" class="form-label">Fecha de Cierre</label>
                        <input type="date" name="fecha_cierre" class="form-control" value="{{ old('fecha_cierre', $fabrica->fecha_cierre) }}">
                    </div>

                    <div class="col-md-12">
                        <label for="observacion" class="form-label">Observaciones</label>
                        <textarea name="observacion" class="form-control" rows="3">{{ old('observacion', $fabrica->observacion) }}</textarea>
                    </div>

                    <div class="col-12 d-flex justify-content-between pt-3">
                        <a href="{{ route('estado1.index') }}" class="btn btn-secondary">Volver</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>
