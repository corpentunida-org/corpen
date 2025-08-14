<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-6">
            
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    {{-- Cabecera adaptada para "Crear" --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-tag fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Nuevo Tipo de Documento</h3>
                        <p class="text-muted">Crea una nueva categoría para organizar archivos.</p>
                    </div>

                    <form action="{{ route('archivo.gdotipodocumento.store') }}" method="POST">
                        @csrf

                        {{-- 
                            Se incluye el formulario pasándole una nueva instancia del modelo.
                            Esto asegura que la variable $tipoDocumento siempre exista en el 'form.blade.php',
                            incluso al crear, evitando errores de "variable no definida".
                        --}}
                        @include('archivo.gdotipodocumento.form', ['tipoDocumento' => new \App\Models\Archivo\GdoTipoDocumento()])

                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-light rounded-pill px-4 py-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift">
                                <i class="bi bi-check-lg me-1"></i> Guardar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-base-layout>