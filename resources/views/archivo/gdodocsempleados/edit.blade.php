<x-base-layout>
    {{-- Contenedor principal que centra el formulario --}}
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-7"> {{-- Una columna más estrecha para un look más refinado --}}
            
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5"> {{-- Más padding para que respire --}}

                    {{-- Cabecera Minimalista --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-pencil-square fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Editar Documento</h3>
                        <p class="text-muted">Actualiza los detalles del registro.</p>
                    </div>

                    {{-- TU DIRECTIVA DE INCLUSIÓN INTACTA --}}
                    @include('archivo.gdodocsempleados.form', [
                        'route' => route('archivo.gdodocsempleados.update', $gdodocsempleado->id),
                        'method' => 'PUT',
                        'doc' => $gdodocsempleado
                    ])

                </div>
            </div>

        </div>
    </div>
    
    {{-- Script para la funcionalidad del input de archivo --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const realFileInput = document.getElementById('archivo-real');
                const displayInput = document.getElementById('archivo-display');
                
                // Al hacer clic en el input de texto, se activa el input de archivo real.
                if(displayInput) {
                    displayInput.addEventListener('click', () => {
                        realFileInput.click();
                    });
                }

                // Cuando se selecciona un archivo, se actualiza el valor del input de texto.
                if(realFileInput) {
                    realFileInput.addEventListener('change', function(e) {
                        const fileName = e.target.files[0] ? e.target.files[0].name : 'No se seleccionó ningún archivo';
                        displayInput.value = fileName;
                    });
                }
            });
        </script>
    @endpush
</x-base-layout>