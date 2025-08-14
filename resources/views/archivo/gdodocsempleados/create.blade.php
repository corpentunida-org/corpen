<x-base-layout>
    {{-- Contenedor principal que centra el formulario --}}
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-7"> {{-- Misma columna estrecha para consistencia --}}
            
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5"> {{-- Mismo padding generoso --}}

                    {{-- Cabecera Minimalista para 'Crear' --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-file-earmark-plus fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Nuevo Documento</h3>
                        <p class="text-muted">Rellena los datos para registrar un nuevo documento.</p>
                    </div>

                    {{-- 
                        LA DIRECTIVA DE INCLUSIÓN CORREGIDA:
                        - 'doc' ahora es un objeto vacío, no nulo.
                        - Esto previene errores de 'propiedad de objeto no definido'.
                    --}}
                    @include('archivo.gdodocsempleados.form', [
                        'route' => route('archivo.gdodocsempleados.store'),
                        'method' => 'POST',
                        'doc' => new \App\Models\Archivo\GdoDocsEmpleados()
                    ])

                </div>
            </div>

        </div>
    </div>
    
    {{-- Script para la funcionalidad del input de archivo. Es el mismo que en 'edit'. --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const realFileInput = document.getElementById('archivo-real');
                const displayInput = document.getElementById('archivo-display');
                
                if(displayInput) {
                    displayInput.addEventListener('click', () => {
                        realFileInput.click();
                    });
                }

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