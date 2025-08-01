<x-base-layout>
    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Formulario de Crédito</h5>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i> Volver
                </a>
            </div>

            {{-- Formulario --}}
            <div class="px-4 pb-4">
                <form action="#" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="tipo_credito" class="form-label">Tipo de Crédito</label>
                        <select name="tipo_credito" id="tipo_credito" class="form-select" required>
                            <option value="">-- Selecciona un tipo --</option>
                            @foreach($tiposCredito as $credito)
                                <option value="{{ $credito->id }}">{{ $credito->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona un tipo de crédito.</div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-send me-2"></i> Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Bootstrap validation
            (() => {
                'use strict';
                const forms = document.querySelectorAll('.needs-validation');
                Array.from(forms).forEach(form => {
                    form.addEventListener('submit', event => {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            })();
        </script>
    @endpush
</x-base-layout>
