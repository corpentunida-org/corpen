<x-base-layout>

    {{-- Mensaje de éxito con diseño mejorado --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Contenedor principal con pestañas --}}
    <div class="card-config">
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

                {{-- ================= PESTAÑA USUARIOS ================= --}}
                <div class="tab-pane fade" id="usuarios-tab" role="tabpanel" aria-labelledby="usuarios-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Usuarios que Asignan Soporte</h5>
                        <a href="{{ route('soportes.usuarios.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tercero</th>
                                    <th>Código Tercero</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Creado el</th>
                                    <th>Actualizado el</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td><span class="id-badge">{{ substr(md5($usuario->id . 'clave-secreta'), 0, 6) }}</span></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">{{ strtoupper(substr($usuario->maeTercero->nom_ter ?? 'N/A', 0, 1)) }}</div>
                                                <span>{{ $usuario->maeTercero->nom_ter ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $usuario->cod_ter ?? '—' }}</span></td>
                                        <td>{{ $usuario->usuario ?? '—' }}</td>
                                        <td>
                                            @if($usuario->estado === 'Activo')
                                                <span class="status-badge status-active">Activo</span>
                                            @else
                                                <span class="status-badge status-inactive">Inactivo</span>
                                            @endif
                                        </td>
                                        <td><span class="text-muted">{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : '—' }}</span></td>
                                        <td><span class="text-muted">{{ $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : '—' }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.usuarios.edit', ['hash' => md5($usuario->id . 'clave-secreta')]) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'usuario', {{ $usuario->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div class="empty-state">
                                                <i class="feather-users"></i>
                                                <p>No hay usuarios registrados</p>
                                                <a href="{{ route('soportes.usuarios.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primer usuario
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $usuarios->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA CATEGORÍAS ================= --}}
                <div class="tab-pane fade" id="categorias-tab" role="tabpanel" aria-labelledby="categorias-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Categorías</h5>
                        <a href="{{ route('soportes.categorias.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nueva
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
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
                                        <td>
                                            <div class="category-info">
                                                <i class="feather-folder category-icon"></i>
                                                <span>{{ $categoria->nombre }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $categoria->descripcion ?? '—' }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.categorias.edit', $categoria) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'categoría', {{ $categoria->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <i class="feather-layers"></i>
                                                <p>No hay categorías registradas</p>
                                                <a href="{{ route('soportes.categorias.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primera categoría
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $categorias->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA TIPOS DE SOPORTE ================= --}}
                <div class="tab-pane fade" id="tipos-soporte-tab" role="tabpanel" aria-labelledby="tipos-soporte-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Tipos de Soporte</h5>
                        <a href="{{ route('soportes.tipos.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tipos as $tipo)
                                    <tr>
                                        <td>
                                            <div class="type-info">
                                                <i class="feather-settings type-icon"></i>
                                                <span>{{ $tipo->nombre }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $tipo->categoria->nombre ?? '—' }}</span></td>
                                        <td><span class="text-muted">{{ $tipo->descripcion ?? '—' }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.tipos.edit', $tipo) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'tipo de soporte', {{ $tipo->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <i class="feather-settings"></i>
                                                <p>No hay tipos de soporte registrados</p>
                                                <a href="{{ route('soportes.tipos.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primer tipo
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $tipos->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA SUBTIPOS DE SOPORTE ================= --}}
                <div class="tab-pane fade" id="subtipos-soporte-tab" role="tabpanel" aria-labelledby="subtipos-soporte-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Subtipos de Soporte</h5>
                        <a href="{{ route('soportes.subtipos.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
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
                                        <td>
                                            <div class="subtype-info">
                                                <i class="feather-git-merge subtype-icon"></i>
                                                <span>{{ $sub->nombre }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $sub->descripcion ?? '—' }}</span></td>
                                        <td><span class="badge bg-light text-dark">{{ $sub->tipo->nombre ?? 'N/A' }}</span></td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.subtipos.edit', $sub) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'subtipo de soporte', {{ $sub->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="feather-git-branch"></i>
                                                <p>No hay subtipos de soporte registrados</p>
                                                <a href="{{ route('soportes.subtipos.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primer subtipo
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $subTipos->links() }}</div>
                    </div>
                </div>
                
                {{-- ================= PESTAÑA ESTADOS ================= --}}
                <div class="tab-pane fade show active" id="estados-tab" role="tabpanel" aria-labelledby="estados-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Estados</h5>
                        <a href="{{ route('soportes.estados.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
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
                                        <td>
                                            <div class="estado-info">
                                                <div class="color-dot" style="background-color: {{ $estado->color }};"></div>
                                                <span>{{ $estado->nombre }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted">{{ $estado->descripcion }}</span></td>
                                        <td>
                                            <span class="color-badge" style="background-color: {{ $estado->color }}; color: {{ (hexdec(substr($estado->color, 1, 2)) * 0.299 + hexdec(substr($estado->color, 3, 2)) * 0.587 + hexdec(substr($estado->color, 5, 2)) * 0.114) > 186 ? '#000' : '#fff' }};">
                                                {{ $estado->color }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.estados.edit', $estado) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'estado', {{ $estado->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="feather-tag"></i>
                                                <p>No hay estados registrados</p>
                                                <a href="{{ route('soportes.estados.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primer estado
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $estados->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA PRIORIDADES ================= --}}
                <div class="tab-pane fade" id="prioridades-tab" role="tabpanel" aria-labelledby="prioridades-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Prioridades</h5>
                        <a href="{{ route('soportes.prioridades.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nueva
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prioridades as $prioridad)
                                    <tr>
                                        <td>
                                            <div class="priority-info">
                                                <i class="feather-chevrons-up priority-icon"></i>
                                                <span>{{ $prioridad->nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.prioridades.edit', $prioridad) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
{{--                                                     <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'prioridad', {{ $prioridad->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <div class="empty-state">
                                                <i class="feather-chevrons-up"></i>
                                                <p>No hay prioridades registradas</p>
                                                <a href="{{ route('soportes.prioridades.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primera prioridad
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-3">{{ $prioridades->links() }}</div>
                    </div>
                </div>

                {{-- ================= PESTAÑA TIPOS OBSERVACIÓN ================= --}}
                <div class="tab-pane fade" id="tipos-obs-tab" role="tabpanel" aria-labelledby="tipos-obs-tab-link">
                    <div class="section-header">
                        <h5 class="section-title">Gestión de Tipos de Observación</h5>
                        <a href="{{ route('soportes.tipoObservaciones.create') }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Crear Nuevo
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-modern">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tiposObservacion as $tipoObs)
                                    <tr>
                                        <td>
                                            <div class="obs-info">
                                                <i class="feather-message-square obs-icon"></i>
                                                <span>{{ $tipoObs->nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="feather-more-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('soportes.tipoObservaciones.edit', $tipoObs) }}">
                                                            <i class="feather-edit me-2"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
    {{--                                                 <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event, 'tipo de observación', {{ $tipoObs->id }})">
                                                            <i class="feather-trash-2 me-2"></i> Eliminar
                                                        </a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            <div class="empty-state">
                                                <i class="feather-message-square"></i>
                                                <p>No hay tipos de observación registrados</p>
                                                <a href="{{ route('soportes.tipoObservaciones.create') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-plus me-1"></i> Crear primer tipo
                                                </a>
                                            </div>
                                        </td>
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

    {{-- Scripts mejorados pero minimalistas --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Mantener pestaña activa
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

                // Confirmaciones mejoradas
                document.body.addEventListener('click', function(e) {
                    const btnCrear = e.target.closest('.btn-primary');
                    if (btnCrear && btnCrear.textContent.includes('Crear')) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Crear nuevo registro?',
                            text: 'Serás redirigido al formulario de creación.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0d6efd',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Continuar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = btnCrear.getAttribute('href');
                            }
                        });
                    }
                });
            });

            // Función de confirmación de eliminación
            function confirmDelete(event, type, id) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `Eliminarás este ${type} permanentemente.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí iría la lógica de eliminación real
                        Swal.fire(
                            '¡Eliminado!',
                            'El registro ha sido eliminado.',
                            'success'
                        );
                    }
                });
            }
        </script>
    @endpush

    {{-- Estilos minimalistas pero profesionales --}}
    @push('styles')
        <style>
            /* Variables sutiles */
            :root {
                --primary-color: #0d6efd;
                --border-color: #e9ecef;
                --text-muted: #6c757d;
                --hover-bg: #f8f9fa;
                --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                --transition: all 0.2s ease;
            }

            /* Tarjeta principal */
            .card-config {
                border: 1px solid var(--border-color);
                border-radius: 0.5rem;
                box-shadow: var(--shadow-sm);
                transition: var(--transition);
            }

            .card-config:hover {
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            /* Header de sección */
            .section-header {
                display: flex;
                justify-content: between;
                align-items: center;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid var(--border-color);
            }

            .section-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin: 0;
                color: #212529;
            }

            /* Tabla moderna */
            .table-modern {
                margin-bottom: 0;
            }

            .table-modern thead th {
                border-bottom: 2px solid var(--border-color);
                font-weight: 600;
                color: #495057;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
                padding: 1rem 0.75rem;
            }

            .table-modern tbody tr {
                transition: var(--transition);
            }

            .table-modern tbody tr:hover {
                background-color: var(--hover-bg);
            }

            .table-modern tbody td {
                padding: 1rem 0.75rem;
                vertical-align: middle;
                border-top: 1px solid var(--border-color);
            }

            /* ID Badge */
            .id-badge {
                font-family: 'Courier New', monospace;
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
                background-color: #f1f3f5;
                border-radius: 0.25rem;
                color: #495057;
            }

            /* Info de usuario */
            .user-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .user-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background-color: var(--primary-color);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.875rem;
            }

            /* Status Badge */
            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 1rem;
                font-size: 0.75rem;
                font-weight: 500;
            }

            .status-active {
                background-color: #d1e7dd;
                color: #0f5132;
            }

            .status-inactive {
                background-color: #f8d7da;
                color: #842029;
            }

            /* Info de categorías y tipos */
            .category-info, .type-info, .subtype-info, .priority-info, .obs-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .category-icon, .type-icon, .subtype-icon, .priority-icon, .obs-icon {
                color: var(--primary-color);
                font-size: 1.125rem;
            }

            /* Info de estado */
            .estado-info {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .color-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                border: 2px solid white;
                box-shadow: 0 0 0 1px var(--border-color);
            }

            .color-badge {
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.75rem;
                font-weight: 500;
            }

            /* Estados vacíos */
            .empty-state {
                text-align: center;
                padding: 3rem 1rem;
            }

            .empty-state i {
                font-size: 3rem;
                color: var(--text-muted);
                margin-bottom: 1rem;
            }

            .empty-state p {
                color: var(--text-muted);
                margin-bottom: 1.5rem;
                font-size: 1rem;
            }

            /* Botones */
            .btn-light {
                background-color: #f8f9fa;
                border-color: #f8f9fa;
                color: #495057;
            }

            .btn-light:hover {
                background-color: #e9ecef;
                border-color: #e9ecef;
            }

            /* Dropdown */
            .dropdown-menu {
                border: 1px solid var(--border-color);
                box-shadow: var(--shadow-sm);
                border-radius: 0.375rem;
            }

            .dropdown-item {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                transition: var(--transition);
            }

            .dropdown-item:hover {
                background-color: var(--hover-bg);
            }

            /* Alert */
            .alert {
                border: none;
                border-radius: 0.5rem;
                padding: 1rem 1.25rem;
                margin-bottom: 1.5rem;
            }

            .alert-success {
                background-color: #d1e7dd;
                color: #0f5132;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .section-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 1rem;
                }

                .table-modern {
                    font-size: 0.875rem;
                }

                .user-info, .category-info, .type-info, .subtype-info, .priority-info, .obs-info {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.25rem;
                }
            }
        </style>
    @endpush
</x-base-layout>