<x-base-layout>
    <div class="app-container">
        <nav class="breadcrumbs">
            <a href="{{ route('correspondencia.tablero') }}" class="crumb-link">Inicio</a>
            <span class="crumb-separator">/</span>
            <a href="{{ route('correspondencia.correspondencias.index') }}" class="crumb-link">Correspondencia</a>
            <span class="crumb-separator">/</span>
            <span class="crumb-current">Editar Radicado</span>
        </nav>

        <header class="main-header">
            <div class="header-content">
                <h1 class="page-title">Modificar Radicado</h1>
                <p class="page-subtitle text-primary">Actualizando: #{{ $correspondencia->id_radicado }}</p>
            </div>
        </header>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.correspondencias.update', $correspondencia) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('correspondencia.correspondencias._form')

                    <hr class="my-4" style="opacity: 0.1;">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- Botón de eliminación con confirmación --}}
                        <!-- 
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash me-1"></i> Eliminar Registro
                        </button>
                         -->

                        <div class="d-flex gap-2">
                            <a href="{{ route('correspondencia.correspondencias.index') }}" class="btn btn-light px-4">Volver</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                <i class="bi bi-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Form oculto para Delete --}}
                <form id="delete-form" action="{{ route('correspondencia.correspondencias.destroy', $correspondencia) }}" method="POST" style="display: none;">
                    @csrf @method('DELETE')
                </form>
            </div>
        </div>
    </div>
    <!-- 
    <script>
        function confirmDelete() {
            if(confirm('¿Está seguro de eliminar este radicado? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
     -->
</x-base-layout>