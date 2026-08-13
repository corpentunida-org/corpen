<x-base-layout>
    @section('titlepage', 'Detalle de Integración')

    <x-success />
    <x-error />

    <!-- ENCABEZADO DE LA API -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 shadow-sm icon">
                    <i class="bi bi-box-arrow-in-right fs-3"></i>
                </div>
                <div>
                    <h2 class="fs-4 fw-bold text-dark mb-1">API Pastors</h2>
                    <span class="text-muted fs-13">{{ env('API_PRODUCCION') }}/api/Pastors</span>
                </div>
            </div>
            <a href="{{ route('integraciones.index') }}" class="btn btn-light rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Volver al Panel
            </a>
        </div>
    </div>

    <div class="row">
        <!-- SECCIÓN DE PRUEBA MANUAL -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                    <h4 class="mb-0 fw-bold text-dark fs-5">Simulador de Petición</h4>
                    <p class="text-muted fs-13 mt-1">Prueba el endpoint con un documento específico.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <!-- Nota: Para que este formulario funcione, debes ajustar tu controlador para que reciba el 'documento' por POST -->
                    <form method="POST" action="{{ route('integraciones.test.pastors') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-medium text-dark">Número de Documento</label>
                            <input type="number" class="form-control bg-light" name="documento" placeholder="Ej: 1077091759" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                            <i class="feather-play me-2"></i> Ejecutar Prueba
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE RESULTADO (Opcional, si devuelves la variable $datos_prueba desde el controlador) -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                    <h4 class="mb-0 fw-bold text-dark fs-5">Respuesta de la API</h4>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(session('resultado_json'))
                        <!-- Muestra el JSON bonito si la petición fue exitosa -->
                        <div class="bg-dark p-3 rounded-3" style="max-height: 300px; overflow-y: auto;">
                            <pre class="text-success mb-0"><code>{{ session('resultado_json') }}</code></pre>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-terminal fs-1 mb-2"></i>
                            <p class="mb-0">Ejecuta una prueba en el panel izquierdo para ver la respuesta aquí.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
