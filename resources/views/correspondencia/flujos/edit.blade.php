<x-base-layout>
    {{-- Estilos de Select2 con tema Bootstrap 5 --}}
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.5rem;
            padding: 0.45rem;
            height: auto;
            border: 1px solid #dee2e6;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #212529;
            padding-left: 0.5rem;
        }
    </style>
    @endpush

    <div class="app-container">
        <header class="main-header mb-4">
            <div class="header-content">
                <h1 class="page-title h3 fw-bold">Editar Flujo de Trabajo</h1>
                <p class="page-subtitle text-muted">
                    Modificando el flujo: <span class="badge bg-primary text-white">{{ $flujo->nombre }}</span>
                </p>
            </div>
        </header>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px; border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.flujos.update', $flujo) }}" method="POST">
                    @csrf 
                    @method('PUT')
                    
                    {{-- Nombre del Flujo --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Flujo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $flujo->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Responsable con Buscador --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Responsable Principal <span class="text-danger">*</span></label>
                        <select name="usuario_id" id="usuario_id_edit" class="form-select select-picker" required>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ old('usuario_id', $flujo->usuario_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-info">
                            <i class="fas fa-info-circle me-1"></i> Puede buscar por nombre o correo electrónico.
                        </div>
                        @error('usuario_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Detalles --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Detalles del Proceso</label>
                        <textarea name="detalle" class="form-control @error('detalle') is-invalid @enderror" 
                                  rows="4" placeholder="Describa el propósito de este flujo...">{{ old('detalle', $flujo->detalle) }}</textarea>
                        @error('detalle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Botones de acción --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('correspondencia.flujos.index') }}" class="btn btn-light border px-4">
                            <i class="fas fa-chevron-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts de Select2 --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#usuario_id_edit').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione un responsable...',
                language: {
                    noResults: function() { return "No se encontraron usuarios coincidentes"; }
                }
            });
        });
    </script>
    @endpush
</x-base-layout>