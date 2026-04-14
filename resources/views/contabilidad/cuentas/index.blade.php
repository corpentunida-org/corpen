<x-base-layout>
    <div class="app-container py-5">
        
        {{-- SECCIÓN 1: MÉTRICAS ULTRAMINIMALISTAS (Mejor contraste) --}}
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border border-light">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-primary rounded-circle">
                            <i class="fas fa-university fs-3 text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-600 fw-bold d-block text-uppercase ls-1" style="font-size: 0.85rem;">Total Entidades</span>
                        <span class="text-dark fw-bolder fs-1 lh-1">{{ $cuentas->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border border-light">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-success rounded-circle">
                            <i class="fas fa-check-circle fs-3 text-success"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-600 fw-bold d-block text-uppercase ls-1" style="font-size: 0.85rem;">Cuentas Activas</span>
                        <span class="text-dark fw-bolder fs-1 lh-1">{{ $cuentas->where('estado', 'Activa')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center p-4 bg-white rounded-3 shadow-sm border border-light">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-info rounded-circle">
                            <i class="fas fa-handshake fs-3 text-info"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-600 fw-bold d-block text-uppercase ls-1" style="font-size: 0.85rem;">Con Convenio</span>
                        <span class="text-dark fw-bolder fs-1 lh-1">{{ $cuentas->whereNotNull('convenios')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Listado Principal --}}
            <div class="col-lg-9">
                <div class="card border-0 bg-transparent">
                    <div class="card-header bg-transparent p-0 pb-4 border-0 d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="fw-bolder m-0 text-dark fs-2">Bancos</h3>
                            <p class="text-muted m-0 fs-6">Listado maestro de cuentas y parámetros</p>
                        </div>
                        <div class="position-relative mt-2 mt-md-0" style="min-width: 250px;">
                            <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 1rem;"></i>
                            <input type="text" id="tableSearch" class="form-control rounded-pill border-light bg-white shadow-xs fs-6" style="padding-left: 2.5rem;" placeholder="Buscar entidad...">
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive bg-white rounded-3 shadow-sm"> {{-- Fondo blanco a toda la tabla --}}
                            <table class="table align-middle gs-0 gy-3 mb-0" id="cuentasTable"> {{-- Reducido el padding vertical (gy-3) --}}
                                <thead>
                                    <tr class="fw-bolder text-gray-800 fs-6 text-uppercase ls-1 border-bottom border-light">
                                        <th class="ps-4 py-3">Banco</th>
                                        <th class="py-3">N° Cuenta</th>
                                        <th class="py-3">Tipo</th>
                                        <th class="py-3">Estado</th>
                                        <th class="py-3">Siasoft</th>
                                        <th class="py-3">Convenios</th>
                                        <th class="text-end pe-4 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-5">
                                    @forelse($cuentas as $cuenta)
                                    <tr class="hover-bg-light transition-3ms border-bottom border-light"> {{-- Hover sutil y línea fina --}}
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $colors = ['bg-light-danger text-danger', 'bg-light-primary text-primary', 'bg-light-warning text-warning', 'bg-light-success text-success', 'bg-light-info text-info'];
                                                    $charColor = $colors[ord(substr($cuenta->banco, 0, 1)) % 5];
                                                @endphp
                                                <div class="symbol symbol-40px me-3"> {{-- Icono ligeramente más pequeño --}}
                                                    <div class="symbol-label {{ $charColor }} rounded-circle fw-bolder fs-5">
                                                        {{ strtoupper(substr($cuenta->banco, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <span class="text-dark fw-bold d-block" style="font-size: 1.05rem;">{{ $cuenta->banco }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center group-hover-action">
                                                <span class="text-dark fw-bold font-monospace me-2" style="font-size: 1.05rem; letter-spacing: 0.5px;">{{ $cuenta->numero_cuenta }}</span>
                                                <button class="btn btn-sm btn-icon btn-clean btn-active-light-primary opacity-0 group-hover-opacity-100 transition-3ms" onclick="copyToClipboard('{{ $cuenta->numero_cuenta }}')" title="Copiar">
                                                    <i class="far fa-copy text-primary fs-5"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-gray-800 fw-bold">{{ $cuenta->tipo_cuenta }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="badge badge-circle {{ $cuenta->estado == 'Activa' ? 'bg-success' : 'bg-danger' }} w-8px h-8px me-2"></div>
                                                <span class="{{ $cuenta->estado == 'Activa' ? 'text-success' : 'text-danger' }} fw-bolder fs-6">{{ $cuenta->estado }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-dark fw-bold bg-light px-2 py-1 rounded">#{{ $cuenta->num_siasoft }}</span>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-gray-800">{{ $cuenta->convenios ?? '-' }}</span>
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" 
                                                    class="btn btn-sm btn-icon btn-light btn-active-light-primary btn-edit-cuenta shadow-sm"
                                                    data-id="{{ $cuenta->id }}"
                                                    data-banco="{{ $cuenta->banco }}"
                                                    data-numero="{{ $cuenta->numero_cuenta }}"
                                                    data-tipo="{{ $cuenta->tipo_cuenta }}"
                                                    data-siasoft="{{ $cuenta->num_siasoft }}"
                                                    data-estado="{{ $cuenta->estado }}"
                                                    data-convenios="{{ $cuenta->convenios }}">
                                                    <i class="fas fa-pencil-alt fs-6"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-15 bg-white">
                                            <i class="fas fa-box-open text-gray-200 fa-4x mb-4"></i>
                                            <span class="text-dark fs-5 fw-bold d-block">No hay cuentas configuradas</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar de Acciones --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm bg-white p-5 mb-4" style="border-radius: 12px;">
                    <button type="button" class="btn btn-primary w-100 rounded-pill fs-5 fw-bolder py-3 shadow-sm mb-4" data-bs-toggle="modal" data-bs-target="#modalCrearCuenta">
                        Registrar Banco
                    </button>
                    
                    <div class="separator separator-dashed my-4 border-gray-300"></div>

                    <div class="d-flex flex-column gap-3">
                        <button onclick="window.print()" class="btn btn-clean btn-active-light-primary text-start fs-6 rounded-pill ps-4 py-3 text-dark fw-bold">
                            <i class="fas fa-print me-3 text-primary fs-5"></i> Imprimir Listado
                        </button>
                        <button class="btn btn-clean btn-active-light-success text-start fs-6 rounded-pill ps-4 py-3 text-dark fw-bold">
                            <i class="fas fa-file-excel me-3 text-success fs-5"></i> Descargar Excel
                        </button>
                    </div>
                </div>

                <div class="p-5 bg-light-warning rounded-3 shadow-xs border border-warning border-opacity-25 text-center">
                    <i class="fas fa-lightbulb text-warning fs-1 mb-3"></i>
                    <p class="fs-6 text-dark mb-0">Verifica los <strong class="fw-bolder">Códigos Siasoft</strong> antes de iniciar una conciliación.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modales --}}
    @include('contabilidad.cuentas.partials.modal_crear')
    @include('contabilidad.cuentas.partials.modal_editar')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, .app-container {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
        }
        
        .transition-3ms { transition: all 0.2s ease-in-out; }
        .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important; }
        .ls-1 { letter-spacing: 0.05em; }
        
        .btn-clean { background-color: transparent; border: none; color: #3f4254; }
        .btn-clean:hover { color: #181C32; background-color: #f3f6f9; }

        .group-hover-action:hover .opacity-0 { opacity: 1 !important; }
        
        /* Ajustes para tabla compacta y unida */
        .hover-bg-light:hover { background-color: #f8f9fa !important; }
        .table { margin-bottom: 0; }
        .table > :not(caption) > * > * { border-bottom-width: 1px; }
        .table tbody tr:last-child td { border-bottom: none; } /* Quita la línea de la última fila */
    </style>
    <script>
        // Buscador dinámico
        $("#tableSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#cuentasTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Copiar al portapapeles
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Número copiado'
                });
            });
        }

        // Lógica de Edición
        $(document).on('click', '.btn-edit-cuenta', function() {
            const data = $(this).data();
            $('#edit_banco').val(data.banco);
            $('#edit_numero_cuenta').val(data.numero);
            $('#edit_tipo_cuenta').val(data.tipo);
            $('#edit_num_siasoft').val(data.siasoft);
            $('#edit_estado').val(data.estado);
            $('#edit_convenios').val(data.convenios);
            
            let action = "{{ route('contabilidad.cuentas-bancarias.update', ['conCuentaBancaria' => ':id']) }}";
            action = action.replace(':id', data.id);
            $('#formEditarCuenta').attr('action', action);
            $('#modalEditarCuenta').modal('show');
        });
    </script>
    @endpush
</x-base-layout>