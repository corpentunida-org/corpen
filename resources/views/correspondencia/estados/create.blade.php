<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 600px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Crear Nuevo Estado</h3>

                <form id="mainForm" action="{{ route('correspondencia.estados.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Estado</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" placeholder="Ej: Radicado / Recibido" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                                  rows="3" placeholder="Explique qué significa este estado...">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg fw-bold shadow">
                            <span id="btnText">Guardar Estado</span>
                            <div id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>
                        <a href="{{ route('correspondencia.estados.index') }}" class="btn btn-light border">Cancelar</a>
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
            document.getElementById('btnText').innerText = "Procesando...";
            document.getElementById('btnSpinner').classList.remove('d-none');
            return true;
        };
    </script>
    @endpush
</x-base-layout>