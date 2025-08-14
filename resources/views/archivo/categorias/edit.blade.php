<x-base-layout>

    {{-- Contenedor principal --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- Tarjeta --}}
            <div class="card card-friendly animate-on-load">

                {{-- Encabezado de la Tarjeta --}}
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-pencil-square fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold mb-0">Editar Categoría de Documento</h4>
                        <small class="text-muted">Modifica la información de la categoría.</small>
                    </div>
                </div>

                {{-- Cuerpo de la Tarjeta --}}
                <div class="card-body p-3 p-lg-4">

                    {{-- Formulario que apunta a la ruta 'update' para actualizar --}}
                    <form action="{{ route('archivo.categorias.update', $categoria) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Directiva para indicar que es una actualización --}}

                        {{-- Inclusión del formulario parcial con el texto del botón --}}
                        @include('archivo.categorias.form', ['btnText' => 'Actualizar Categoría'])
                    </form>

                </div>
            </div>

        </div>
    </div>

</x-base-layout>