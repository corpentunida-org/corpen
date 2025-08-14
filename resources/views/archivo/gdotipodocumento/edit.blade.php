<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-6"> {{-- Columna más estrecha, ideal para formularios simples --}}
            
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    {{-- Cabecera Minimalista --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-tags-fill fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Editar Tipo de Documento</h3>
                        <p class="text-muted">Ajusta el nombre de la categoría.</p>
                    </div>

                    <form action="{{ route('archivo.gdotipodocumento.update', $tipoDocumento->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Se incluye el nuevo y limpio formulario --}}
                        @include('archivo.gdotipodocumento.form')

                        {{-- Separador y botones de acción refinados --}}
                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-light rounded-pill px-4 py-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift">
                                <i class="bi bi-check-lg me-1"></i> Actualizar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-base-layout>