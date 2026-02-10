<x-base-layout>
    {{-- Estilos de Select2 --}}
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.5rem;
            padding: 0.5rem;
            height: auto;
        }
    </style>
    @endpush

    <div class="app-container">
        <header class="main-header mb-4">
            <div class="header-content">
                <h1 class="page-title h3 fw-bold">Crear Flujo de Trabajo</h1>
                <p class="page-subtitle text-muted">Configure un nuevo esquema de pasos para la correspondencia.</p>
            </div>
        </header>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px; border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.flujos.store') }}" method="POST">
                    @csrf
                    
                    {{-- Nombre del Flujo --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Flujo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               placeholder="Ej: Aprobación de Facturas" value="{{ old('nombre') }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Responsable con Buscador --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Responsable Principal <span class="text-danger">*</span></label>
                        <select name="usuario_id" id="usuario_id" class="form-select select-picker" required>
                            <option value="">Escriba para buscar un usuario...</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" {{ old('usuario_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Busque por nombre o correo electrónico.</div>
                        @error('usuario_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    {{-- Detalles --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Detalles del Proceso</label>
                        <textarea name="detalle" class="form-control" rows="4" placeholder="Describa brevemente cómo funciona este flujo...">{{ old('detalle') }}</textarea>
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Crear Flujo
                        </button>
                        <a href="{{ route('correspondencia.flujos.index') }}" class="btn btn-light border px-4">Cancelar</a>
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
            $('#usuario_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione un responsable...',
                language: {
                    noResults: function() { return "No se encontraron usuarios"; }
                }
            });
        });
    </script>
    @endpush
</x-base-layout>