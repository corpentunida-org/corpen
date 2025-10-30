<x-base-layout>
    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0"></h5>
                <a href="{{ route('soportes.soportes.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Volver al Listado</span>
                </a>
            </div>

            <form action="{{ route('soportes.soportes.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @include('soportes.soportes.form')
            </form>
        </div>
    </div>
</x-base-layout>
