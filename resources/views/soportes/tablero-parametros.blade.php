<x-base-layout>

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Contenedor principal con pestañas --}}
    <div class="card">
        {{-- Navegación de las pestañas --}}
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="soporteConfigTab" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="usuarios-tab-link" data-bs-toggle="tab" data-bs-target="#usuarios-tab" type="button" role="tab" aria-controls="usuarios-tab" aria-selected="false">
                        <i class="feather-users me-2"></i>Usuarios
                    </button>
                </li> 
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="categorias-tab-link" data-bs-toggle="tab" data-bs-target="#categorias-tab" type="button" role="tab" aria-controls="categorias-tab" aria-selected="false">
                        <i class="feather-layers me-2"></i>Categorías
                    </button>
                </li>             
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tipos-soporte-tab-link" data-bs-toggle="tab" data-bs-target="#tipos-soporte-tab" type="button" role="tab" aria-controls="tipos-soporte-tab" aria-selected="false">
                        <i class="feather-settings me-2"></i>Tipos de Soporte
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="subtipos-soporte-tab-link" data-bs-toggle="tab" data-bs-target="#subtipos-soporte-tab" type="button" role="tab" aria-controls="subtipos-soporte-tab" aria-selected="false">
                        <i class="feather-git-merge me-2"></i>Subtipos de Soporte
                    </button>
                </li>        
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="estados-tab-link" data-bs-toggle="tab" data-bs-target="#estados-tab" type="button" role="tab" aria-controls="estados-tab" aria-selected="true">
                        <i class="feather-tag me-2"></i>Estados
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prioridades-tab-link" data-bs-toggle="tab" data-bs-target="#prioridades-tab" type="button" role="tab" aria-controls="prioridades-tab" aria-selected="false">
                        <i class="feather-chevrons-up me-2"></i>Prioridades
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tipos-obs-tab-link" data-bs-toggle="tab" data-bs-target="#tipos-obs-tab" type="button" role="tab" aria-controls="tipos-obs-tab" aria-selected="false">
                        <i class="feather-message-square me-2"></i>Tipos de Observación
                    </button>
                </li>
            </ul>
        </div>

        {{-- Contenido de las pestañas --}}
        <div class="card-body">
            <div class="tab-content" id="soporteConfigTabContent">

                {{-- ================= PESTAÑA USUARIOS QUE ASIGNAN SOPORTE ================= --}}
                <div class="tab-pane fade" id="usuarios-tab" role="tabpanel" aria-labelledby="usuarios-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Usuarios que Asignan Soporte</h5>
                        <a href="{{ route('soportes.usuarios.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Area de Soporte</th>
                                    <th>Estado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->maeTercero->nom_ter ?? '—' }}</td>
                                        <td>{{ $usuario->maeTercero->correo ?? '—' }}</td>
                                        <td>{{ $usuario->rol ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-success">Activo</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.usuarios.edit', $usuario) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.usuarios.destroy', $usuario) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay usuarios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Paginación --}}
                        <div class="mt-3">{{ $usuarios->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA CATEGORÍAS ================= --}}
                <div class="tab-pane fade" id="categorias-tab" role="tabpanel" aria-labelledby="categorias-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Categorías</h5>
                        <a href="{{ route('soportes.categorias.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nueva
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->nombre }}</td>
                                        <td>{{ $categoria->descripcion ?? '—' }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.categorias.edit', $categoria) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.categorias.destroy', $categoria) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay categorías registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $categorias->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA TIPOS DE SOPORTE ================= --}}
                <div class="tab-pane fade" id="tipos-soporte-tab" role="tabpanel" aria-labelledby="tipos-soporte-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Tipos de Soporte</h5>
                        <a href="{{ route('soportes.tipos.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tipos as $tipo)
                                    <tr>
                                        <td>{{ $tipo->nombre }}</td>
                                        <td>{{ $tipo->descripcion ?? '—' }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.tipos.edit', $tipo) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.tipos.destroy', $tipo) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay tipos de soporte registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $tipos->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA SUBTIPOS DE SOPORTE ================= --}}
                <div class="tab-pane fade" id="subtipos-soporte-tab" role="tabpanel" aria-labelledby="subtipos-soporte-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Subtipos de Soporte</h5>
                        <a href="{{ route('soportes.subtipos.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Tipo Padre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subTipos as $sub)
                                    <tr>
                                        <td>{{ $sub->nombre }}</td>
                                        <td>{{ $sub->descripcion ?? '—' }}</td>
                                        <td><span class="badge bg-secondary">{{ $sub->tipo->nombre ?? 'N/A' }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.subtipos.edit', $sub) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.subtipos.destroy', $sub) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay subtipos de soporte registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $subTipos->links() }}</div>
                    </div>
                </div>
                
                {{-- ================= PESTAÑA ESTADOS ================= --}}
                <div class="tab-pane fade show active" id="estados-tab" role="tabpanel" aria-labelledby="estados-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Estados</h5>
                        <a href="{{ route('soportes.estados.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Color</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($estados as $estado)
                                    <tr>
                                        <td>{{ $estado->nombre }}</td>
                                        <td>{{ $estado->descripcion }}</td>
                                        <td>
                                            <span class="badge d-flex align-items-center" style="background-color: {{ $estado->color }}; color: {{ (hexdec(substr($estado->color, 1, 2)) * 0.299 + hexdec(substr($estado->color, 3, 2)) * 0.587 + hexdec(substr($estado->color, 5, 2)) * 0.114) > 186 ? '#000' : '#fff' }};">
                                                <i class="feather-circle me-2" style="fill: {{ $estado->color }};"></i> {{ $estado->color }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.estados.edit', $estado) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.estados.destroy', $estado) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay estados registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $estados->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA PRIORIDADES ================= --}}
                <div class="tab-pane fade" id="prioridades-tab" role="tabpanel" aria-labelledby="prioridades-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Prioridades</h5>
                        <a href="{{ route('soportes.prioridades.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nueva
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prioridades as $prioridad)
                                    <tr>
                                        <td>{{ $prioridad->nombre }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.prioridades.edit', $prioridad) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.prioridades.destroy', $prioridad) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No hay prioridades registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $prioridades->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA TIPOS OBSERVACIÓN ================= --}}
                <div class="tab-pane fade" id="tipos-obs-tab" role="tabpanel" aria-labelledby="tipos-obs-tab-link">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Gestión de Tipos de Observación</h5>
                        <a href="{{ route('soportes.tipoObservaciones.create') }}" class="btn btn-success btn-sm btnCrear">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tiposObservacion as $tipoObs)
                                    <tr>
                                        <td>{{ $tipoObs->nombre }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item btnEditar" href="{{ route('soportes.tipoObservaciones.edit', $tipoObs) }}">
                                                            <i class="feather-edit-3 me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('soportes.tipoObservaciones.destroy', $tipoObs) }}" method="POST" class="formEliminar d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="feather-trash-2 me-2"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No hay tipos de observación registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $tiposObservacion->links() }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Scripts de confirmación con SweetAlert2 --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ----------------------- SweetAlert2 -----------------------
                function confirmarAccion(config, callback) {
                    Swal.fire({
                        title: config.title,
                        text: config.text,
                        icon: config.icon,
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, continuar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            callback();
                        }
                    });
                }

                document.body.addEventListener('click', function(e) {
                    const btnCrear = e.target.closest('.btnCrear');
                    if (btnCrear) {
                        e.preventDefault();
                        confirmarAccion({
                            title: '¿Crear nuevo registro?',
                            text: 'Serás redirigido al formulario de creación.',
                            icon: 'info'
                        }, () => {
                            window.location.href = btnCrear.getAttribute('href');
                        });
                    }

                    const btnEditar = e.target.closest('.btnEditar');
                    if (btnEditar) {
                        e.preventDefault();
                        confirmarAccion({
                            title: '¿Editar este registro?',
                            text: 'Serás redirigido al formulario de edición.',
                            icon: 'question'
                        }, () => {
                            window.location.href = btnEditar.getAttribute('href');
                        });
                    }
                });

                document.querySelectorAll('.formEliminar').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        confirmarAccion({
                            title: '¿Estás seguro?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'warning'
                        }, () => {
                            form.submit();
                        });
                    });
                });

                // ----------------------- Mantener pestaña activa -----------------------
                const activeTab = localStorage.getItem('activeTab');
                if (activeTab) {
                    const tabTriggerEl = document.querySelector(`[data-bs-target="${activeTab}"]`);
                    if (tabTriggerEl) {
                        new bootstrap.Tab(tabTriggerEl).show();
                    }
                }

                document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('shown.bs.tab', function (event) {
                        localStorage.setItem('activeTab', event.target.getAttribute('data-bs-target'));
                    });
                });

            });
        </script>
    @endpush

</x-base-layout>
