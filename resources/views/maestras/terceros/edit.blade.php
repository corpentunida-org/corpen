<x-base-layout>
    @section('titlepage', 'Editar Tercero')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="d-flex gap-4 align-items-center">
                    <div class="avatar-text avatar-lg bg-gray-200">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ $tercero->nom_ter }}</div>
                        <h3 class="fs-13 fw-semibold text-truncate-1-line">
                            Cédula: {{ $tercero->cod_ter }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('maestras.terceros.form', [
        'action' => route('maestras.terceros.update', $tercero->cod_ter),
        'method' => 'PUT',
        'tercero' => $tercero,
        'buttonText' => 'Actualizar Tercero'
    ])
</x-base-layout>
