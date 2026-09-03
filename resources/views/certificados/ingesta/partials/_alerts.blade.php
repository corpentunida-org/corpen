{{-- ALERTAS FLASH --}}
@if(session('success'))
    <div class="alert-g alert-ok mb-4" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="alert-g alert-err mb-4" role="alert">
        <i class="fas fa-times-circle fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if(session('warning'))
    <div class="alert-g alert-warn mb-4" role="alert">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <span>{{ session('warning') }}</span>
    </div>
@endif

<!-- Mostrar Botón de Crear Terceros de forma independiente -->
@if(session('requiere_crear_terceros') && session('bloque_fallido'))
    <div class="alert alert-warning mb-4 d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-exclamation-triangle"></i>
            <strong>¡Atención!</strong> Se requiere registrar estos clientes en la Maestra de Terceros para poder continuar.
        </div>
        <!-- Botón que abre el modal (Asegúrate de usar data-bs-toggle si es Bootstrap 5 o data-toggle si es BS4) -->
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearTerceros">
            <i class="fas fa-eye"></i> Previsualizar y Crear Terceros
        </button>
    </div>

    <!-- Modal de Previsualización -->
    <div class="modal fade" id="modalCrearTerceros" tabindex="-1" aria-labelledby="modalCrearTercerosLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark" id="modalCrearTercerosLabel">
                        <i class="fas fa-users"></i> Previsualización de Nuevos Terceros
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Se insertarán los siguientes registros en la tabla <code>MaeTerceros</code> con valores genéricos seguros. Revisa los datos antes de proceder.</p>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Cédula/NIT (cod_ter)</th>
                                    <th>Razón Social / Nombre (nom_ter)</th>
                                    <th>Tipo Persona</th>
                                    <th>Tipo Tercero</th>
                                    <th>Tipo Doc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(session('lista_faltantes'))
                                    @foreach(session('lista_faltantes') as $faltante)
                                        <tr>
                                            <td><strong>{{ $faltante['tercero'] }}</strong></td>
                                            <td>{{ $faltante['nombre_tercero'] ?: 'SIN NOMBRE REGISTRADO' }}</td>
                                            <td>1 (Natural)</td>
                                            <td>CLIENTE</td>
                                            <td>13 (C.C.)</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <!-- Formulario real de inyección con bloqueo de botón -->
                    <form action="{{ route('certificados.ingesta.crear_terceros') }}" method="POST" class="m-0" onsubmit="return procesarCreacion(this)">
                        @csrf
                        <input type="hidden" name="bloque_origen" value="{{ session('bloque_fallido') }}">
                        <button type="submit" id="btnConfirmarTerceros" class="btn btn-success">
                            <i class="fas fa-check"></i> Confirmar Inserción
                        </button>
                    </form>
                    <!-- Script para bloquear el botón y mostrar el loading -->
                    <script>
                        function procesarCreacion(formulario) {
                            if (confirm('¿Confirmas la creación de estos registros únicos en la maestra?')) {
                                let boton = document.getElementById('btnConfirmarTerceros');
                                boton.disabled = true;
                                boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando datos...';
                                return true;
                            }
                            return false;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endif
