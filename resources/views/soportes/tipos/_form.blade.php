{{-- resources/views/soportes/tipos/_form.blade.php --}}

<style>
    /* 🎨 Estilos personalizados para el formulario */
    .card-form {
        background: #fff;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 5px 15px rgba(138, 43, 226, 0.15);
        transition: 0.3s ease-in-out;
    }

    .card-form:hover {
        box-shadow: 0 8px 20px rgba(138, 43, 226, 0.25);
    }

    .form-label {
        font-weight: 600;
        color: #5e5a78;
    }

    .form-control, .form-select {
        border-radius: 0.7rem;
        border: 1px solid #c7b9f3;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: #8A2BE2;
        box-shadow: 0 0 5px rgba(138, 43, 226, 0.3);
    }

    .btn-success {
        background-color: #8A2BE2;
        border-color: #8A2BE2;
        border-radius: 0.7rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: #7a25cb;
        box-shadow: 0 4px 12px rgba(138, 43, 226, 0.3);
    }

    .btn-light {
        border-radius: 0.7rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background-color: #f3eaff;
        border-color: #8A2BE2;
        color: #8A2BE2;
    }

    textarea {
        resize: none;
    }
</style>

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
    @csrf
    @if (isset($tipo))
        @method('PUT')
    @endif

    <div class="card-form">
        <div class="row">
            {{-- Campo para Nombre --}}
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label text-capitalize">Nombre</label>
                <input type="text" 
                       class="form-control @error('nombre') is-invalid @enderror" 
                       id="nombre" name="nombre" 
                       value="{{ old('nombre', $tipo->nombre ?? '') }}" 
                       placeholder="Ej: Mantenimiento Preventivo" required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Campo para Categoría --}}
            <div class="col-md-6 mb-3">
                <label for="id_categoria" class="form-label text-capitalize">Categoría</label>
                <select name="id_categoria" id="id_categoria" 
                        class="form-select @error('id_categoria') is-invalid @enderror" required>
                    <option value="" disabled {{ old('id_categoria', $tipo->id_categoria ?? '') == '' ? 'selected' : '' }}>
                        Selecciona una categoría
                    </option>
                    @foreach ($categorias as $id => $nombre)
                        <option value="{{ $id }}" 
                            {{ old('id_categoria', $tipo->id_categoria ?? null) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_categoria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Campo para Descripción --}}
            <div class="col-md-12 mb-3">
                <label for="descripcion" class="form-label text-capitalize">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" 
                          class="form-control @error('descripcion') is-invalid @enderror"
                          placeholder="Describe brevemente en qué consiste este tipo de soporte.">{{ old('descripcion', $tipo->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex flex-row-reverse gap-2 mt-4">
            <button class="btn btn-success" type="submit">
                <i class="feather-save me-2"></i> 
                {{ isset($tipo) ? 'Actualizar Tipo' : 'Crear Tipo' }}
            </button>
            <a href="{{ route('soportes.tablero') }}" class="btn btn-light">Cancelar</a>
        </div>
    </div>
</form>
