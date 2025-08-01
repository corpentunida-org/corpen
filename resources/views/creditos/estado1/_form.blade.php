<form action="{{ $route }}" method="POST">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $fabrica->nombre ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label>Cuenta:</label>
        <input type="text" name="cuenta" class="form-control" value="{{ old('cuenta', $fabrica->cuenta ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Tipo:</label>
        <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $fabrica->tipo ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Tasa de interés:</label>
        <input type="number" step="0.01" name="tasa_interes" class="form-control" value="{{ old('tasa_interes', $fabrica->tasa_interes ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Plazo máximo:</label>
        <input type="number" name="plazo_maximo" class="form-control" value="{{ old('plazo_maximo', $fabrica->plazo_maximo ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Plazo mínimo:</label>
        <input type="number" name="plazo_minimo" class="form-control" value="{{ old('plazo_minimo', $fabrica->plazo_minimo ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Edad mínima:</label>
        <input type="number" name="edad_minima" class="form-control" value="{{ old('edad_minima', $fabrica->edad_minima ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Edad máxima:</label>
        <input type="number" name="edad_maxima" class="form-control" value="{{ old('edad_maxima', $fabrica->edad_maxima ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Fecha de apertura:</label>
        <input type="date" name="fecha_apertura" class="form-control" value="{{ old('fecha_apertura', $fabrica->fecha_apertura ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Fecha de cierre:</label>
        <input type="date" name="fecha_cierre" class="form-control" value="{{ old('fecha_cierre', $fabrica->fecha_cierre ?? '') }}">
    </div>

    <div class="mb-3">
        <label>Observación:</label>
        <textarea name="observacion" class="form-control">{{ old('observacion', $fabrica->observacion ?? '') }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="{{ route('estado1.index') }}" class="btn btn-secondary">Volver</a>
</form>
