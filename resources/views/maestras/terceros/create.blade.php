<x-base-layout>
    @section('titlepage', 'Crear Tercero')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div>
                        <h3 class="mb-0">Nuevo Tercero</h3>
                        <span class="text-muted">Complete los datos requeridos. Los campos marcados con (*) son obligatorios.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('maestras.terceros.form', [
        'action' => route('maestras.terceros.store'),
        'buttonText' => 'Guardar Tercero'
    ])
</x-base-layout>
