<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 600px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4 text-warning">Editar Estado</h3>

                <form id="mainForm" action="{{ route('correspondencia.estados.update', $estado) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Estado</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $estado->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $estado->descripcion) }}</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="submitBtn" class="btn btn-warning btn-lg fw-bold shadow text-white">
                            <span id="btnText">Actualizar Datos</span>
                            <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>
                        <a href="{{ route('correspondencia.estados.index') }}" class="btn btn-light border">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('mainForm').onsubmit = function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            document.getElementById('btnText').innerText = "Actualizando...";
            document.getElementById('btnSpinner').classList.remove('d-none');
            return true;
        };
    </script>
    @endpush
</x-base-layout>