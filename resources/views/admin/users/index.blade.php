<x-base-layout>
    @section('titlepage', 'Gestión de Usuarios')
    
    <x-success />
    <x-error />

    <style>
        /* Contenedor Principal */
        .ui-pro-card {
            background: #ffffff;
            border: 1px solid #eaedf1;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04);
            overflow: hidden;
        }

        /* Avatar Dinámico */
        .ui-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .ui-avatar-initials {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #eff6ff; /* bg-blue-50 */
            color: #2563eb; /* text-blue-600 */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        /* Botón de Acción Principal */
        .ui-btn-create {
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .ui-btn-create:hover {
            background: #059669;
            transform: translateY(-1px);
            color: #fff;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        /* Botón de Fila (Acción) */
        .ui-btn-action {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .ui-btn-action:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: scale(1.05);
        }

        /* ---------------------------------------------------
           SKIN PARA DATATABLES (El secreto del rediseño Pro)
           --------------------------------------------------- */
        
        /* Contenedor de Búsqueda y Paginación (Header/Footer de DataTables) */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem !important;
            color: #1e293b !important;
            outline: none !important;
            transition: border-color 0.2s !important;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 0.4rem 2rem 0.4rem 1rem !important;
            font-size: 0.9rem !important;
            outline: none !important;
        }
        
        /* Ajuste de espaciado para DataTables */
        .dataTables_wrapper .row:first-child {
            margin-bottom: 1.5rem;
            align-items: center;
        }
        .dataTables_wrapper .row:last-child {
            margin-top: 1.5rem;
            align-items: center;
        }

        /* Estilo de la Tabla */
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        table.dataTable thead th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 1rem 1.5rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
        }
        table.dataTable tbody td {
            padding: 1.25rem 1.5rem !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #334155 !important;
            transition: background 0.2s ease;
        }
        table.dataTable tbody tr:hover td {
            background-color: #f8fafc !important;
        }
        
        /* Paginación DataTables estilo moderno */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            border: 1px solid transparent !important;
            padding: 0.4rem 0.8rem !important;
            margin: 0 2px !important;
            color: #475569 !important;
            font-weight: 500 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
    </style>

    <div class="col-12 mb-4">
        <div class="ui-pro-card">
            
            <div class="card-header bg-white border-bottom border-light-subtle d-flex flex-column flex-md-row justify-content-between align-items-md-center py-4 px-4 px-md-5">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center rounded-3 me-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-1">Directorio de Usuarios</h4>
                        <p class="text-muted fs-14 mb-0">Gestione los accesos y perfiles corporativos del sistema.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="d-none d-sm-flex align-items-center gap-2 border-end pe-3 border-light-subtle">
                        <a href="javascript:location.reload();" class="text-muted text-decoration-none px-2 py-1 bg-light rounded" data-bs-toggle="tooltip" title="Actualizar Datos">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                    
                    <a class="ui-btn-create text-decoration-none" href="{{ route('admin.users.create') }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Usuario</span>
                    </a>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="table-responsive overflow-visible">
                    <table class="table align-middle w-100" id="projectList">
                        <thead>
                            <tr>
                                <th>Información del Usuario</th>
                                <th class="text-end text-md-center" style="width: 100px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if (isset($user->ubicacion_foto) && $user->ubicacion_foto)
                                                    <img src="{{ route('archivo.empleado.verFoto', $user->id) }}" class="ui-avatar">
                                                @else
                                                    @php
                                                        $nombres = explode(' ', trim($user->name));
                                                        $iniciales = strtoupper(substr($nombres[0] ?? '?', 0, 1) . substr($nombres[1] ?? '', 0, 1));
                                                    @endphp
                                                    <div class="ui-avatar-initials">
                                                        {{ $iniciales }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="fw-bold fs-15 text-dark text-decoration-none d-block mb-1">
                                                    {{ strtoupper($user->name) }}
                                                </a>
                                                <div class="d-flex align-items-center text-muted fs-13">
                                                    <i class="bi bi-envelope me-2 opacity-75"></i> 
                                                    {{ strtolower($user->email) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-end text-md-center">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="ui-btn-action" data-bs-toggle="tooltip" title="Administrar Usuario">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
</x-base-layout>