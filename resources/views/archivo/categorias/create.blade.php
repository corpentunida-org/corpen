<x-base-layout>

    {{-- Contenedor principal --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- Tarjeta --}}
            <div class="card card-friendly animate-on-load">

                {{-- Encabezado de la Tarjeta --}}
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-plus-circle fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold mb-0">Nueva Categoría de Documento</h4>
                        <small class="text-muted">Completa la información para registrar una nueva categoría.</small>
                    </div>
                </div>

                {{-- Cuerpo de la Tarjeta --}}
                <div class="card-body p-3 p-lg-4">

                    {{-- Formulario que apunta a la ruta 'store' para guardar --}}
                    <form action="{{ route('archivo.categorias.store') }}" method="POST">
                        @csrf

                        {{-- Inclusión del formulario parcial con el texto del botón --}}
                        @include('archivo.categorias.form', ['btnText' => 'Crear Categoría'])
                    </form>

                </div>
            </div>

        </div>
    </div>

</x-base-layout>