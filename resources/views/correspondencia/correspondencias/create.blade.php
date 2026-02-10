<x-base-layout>
    <div class="app-container">
        <nav class="breadcrumbs">
            <a href="{{ route('correspondencia.tablero') }}" class="crumb-link">Inicio</a>
            <span class="crumb-separator">/</span>
            <a href="{{ route('correspondencia.correspondencias.index') }}" class="crumb-link">Correspondencia</a>
            <span class="crumb-separator">/</span>
            <span class="crumb-current">Radicar Nuevo Documento</span>
        </nav>

        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Nuevo Radicado</h1>
                <p class="page-subtitle">Complete la información para ingresar el documento al flujo de trabajo.</p>
            </div>
        </header>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.correspondencias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
@if ($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f87171;">
        <strong>Atención: El formulario tiene errores:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    @include('correspondencia.correspondencias._form')

                    <hr class="my-4" style="opacity: 0.1;">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('correspondencia.correspondencias.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Guardar Radicado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>