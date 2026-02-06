<x-base-layout>
    
    {{-- MENSAJES FLASH --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050; min-width: 300px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle me-2"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1050; min-width: 300px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="container py-4">
        <div class="row g-4">
            
            {{-- COLUMNA IZQUIERDA: FORMULARIOS --}}
            <div class="col-lg-4">
                <div class="card border rounded-0 sticky-top" style="top: 20px;">
                    <!-- PESTAÑAS -->
                    <ul class="nav nav-tabs" id="leftTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#form-main" type="button" role="tab" aria-controls="form-main" aria-selected="true">
                                Vincular
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cats-tab" data-bs-toggle="tab" data-bs-target="#form-cats" type="button" role="tab" aria-controls="form-cats" aria-selected="false">
                                Catálogos
                            </button>
                        </li>
                    </ul>
                    
                    <!-- CONTENIDO DE PESTAÑAS -->
                    <div class="tab-content p-4" id="leftTabContent">
                        
                        {{-- 1. FORMULARIO PRINCIPAL (SUBGRUPOS) --}}
                        <div class="tab-pane fade show active" id="form-main" role="tabpanel" aria-labelledby="main-tab">
                            <div class="mb-4">
                                <h3 class="mb-1" id="title-main">Nueva Vinculación</h3>
                                <p class="text-muted small mb-0">Cree la categoría final uniendo los catálogos.</p>
                            </div>
                            
                            <form id="formMain" action="{{ route('inventario.clasificacion.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="methodMain" value="POST">
                                
                                <div class="mb-3">
                                    <label for="inpMainName" class="form-label">Nombre</label>
                                    <input type="text" name="nombre" id="inpMainName" class="form-control" required placeholder="Ej: Portátiles">
                                </div>

                                <div class="mb-3">
                                    <label for="inpMainDescripcion" class="form-label">Descripción</label>
                                    <textarea name="descripcion" id="inpMainDescripcion" class="form-control" rows="3" placeholder="Descripción detallada del subgrupo..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="selMainTipo" class="form-label">Tipo</label>
                                    <select name="id_InvTipos" id="selMainTipo" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="selMainLinea" class="form-label">Línea</label>
                                    <select name="id_InvLineas" id="selMainLinea" class="form-select" required>
                                        <option value="">Seleccione primero un Tipo...</option>
                                        @foreach($lineas as $l) 
                                            <option value="{{ $l->id }}" data-tipo="{{ $l->id_InvTipos }}">{{ $l->nombre }}</option> 
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="selMainGrupo" class="form-label">Grupo</label>
                                    <select name="id_InvGrupos" id="selMainGrupo" class="form-select" required>
                                        <option value="">Seleccione primero una Línea...</option>
                                        @foreach($grupos as $g) 
                                            <option value="{{ $g->id }}" data-linea="{{ $g->id_InvLineas }}">{{ $g->nombre }}</option> 
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary-pastel" id="btnMain">
                                        Guardar
                                    </button>
                                    <button type="button" onclick="resetMain()" class="btn btn-secondary-pastel d-none" id="cancelMain">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- 2. FORMULARIOS CATÁLOGOS BASE --}}
                        <div class="tab-pane fade" id="form-cats" role="tabpanel" aria-labelledby="cats-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="m-0">Catálogos Base</h3>
                                <button type="button" onclick="resetCats()" class="btn btn-sm btn-secondary-pastel">
                                    Resetear
                                </button>
                            </div>

                            {{-- Form TIPO --}}
                            <div class="mb-3 border-bottom pb-3" id="box-tipo">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">TIPO</span>
                                    <span class="badge bg-blue-pastel" id="lbl-tipo">NUEVO</span>
                                </div>
                                <form id="formTipo" action="{{ route('inventario.clasificacion.tipo.store') }}" method="POST">
                                    @csrf <input type="hidden" name="_method" id="methodTipo" value="POST">
                                    <div class="mb-2">
                                        <input type="text" name="nombre" id="inpTipoName" class="form-control" placeholder="Nombre del tipo" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="descripcion" id="inpTipoDescripcion" class="form-control" rows="2" placeholder="Descripción del tipo..."></textarea>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-blue-pastel" type="submit">Guardar</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Form LÍNEA --}}
                            <div class="mb-3 border-bottom pb-3" id="box-linea">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">LÍNEA</span>
                                    <span class="badge bg-green-pastel" id="lbl-linea">NUEVO</span>
                                </div>
                                <form id="formLinea" action="{{ route('inventario.clasificacion.linea.store') }}" method="POST">
                                    @csrf <input type="hidden" name="_method" id="methodLinea" value="POST">
                                    <div class="mb-2">
                                        <select name="id_InvTipos" id="inpLineaParent" class="form-select" required>
                                            <option value="">Pertenece a Tipo...</option>
                                            @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="nombre" id="inpLineaName" class="form-control" placeholder="Nombre de la línea" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="descripcion" id="inpLineaDescripcion" class="form-control" rows="2" placeholder="Descripción de la línea..."></textarea>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-green-pastel" type="submit">Guardar</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Form GRUPO --}}
                            <div class="mb-3" id="box-grupo">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">GRUPO</span>
                                    <span class="badge bg-purple-pastel" id="lbl-grupo">NUEVO</span>
                                </div>
                                <form id="formGrupo" action="{{ route('inventario.clasificacion.grupo.store') }}" method="POST">
                                    @csrf <input type="hidden" name="_method" id="methodGrupo" value="POST">
                                    <div class="mb-2">
                                        <select name="id_InvLineas" id="inpGrupoParent" class="form-select" required>
                                            <option value="">Pertenece a Línea...</option>
                                            @foreach($lineas as $l) <option value="{{ $l->id }}">{{ $l->nombre }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="nombre" id="inpGrupoName" class="form-control" placeholder="Nombre del grupo" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="descripcion" id="inpGrupoDescripcion" class="form-control" rows="2" placeholder="Descripción del grupo..."></textarea>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-purple-pastel" type="submit">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: LISTADOS DE AUDITORÍA --}}
            <div class="col-lg-8">
                <div class="card border rounded-0">
                    <!-- PESTAÑAS -->
                    <ul class="nav nav-tabs" id="rightTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sub-tab" data-bs-toggle="tab" data-bs-target="#list-sub" type="button" role="tab" aria-controls="list-sub" aria-selected="true">
                                Subgrupos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#list-grupos" type="button" role="tab" aria-controls="list-grupos" aria-selected="false">
                                Grupos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lineas-tab" data-bs-toggle="tab" data-bs-target="#list-lineas" type="button" role="tab" aria-controls="list-linea" aria-selected="false">
                                Líneas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tipos-tab" data-bs-toggle="tab" data-bs-target="#list-tipos" type="button" role="tab" aria-controls="list-tipos" aria-selected="false">
                                Tipos
                            </button>
                        </li>
                    </ul>
                    
                    <!-- CONTENIDO DE PESTAÑAS -->
                    <div class="tab-content p-0" id="rightTabContent">
                        
                        {{-- 1. TABLA SUBGRUPOS --}}
                        <div class="tab-pane fade show active" id="list-sub" role="tabpanel" aria-labelledby="sub-tab">
                            <!-- SECCIÓN DE FILTROS PARA SUBGRUPOS -->
                            <div class="p-3 border-bottom bg-light-pastel">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="search-subgrupo" placeholder="Buscar...">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="filter-tipo-sub" class="form-select">
                                            <option value="">Todos los Tipos</option>
                                            @foreach($tipos_list as $t)
                                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="filter-linea-sub" class="form-select">
                                            <option value="">Todas las Líneas</option>
                                            @foreach($lineas_list as $l)
                                            <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="filter-grupo-sub" class="form-select">
                                            <option value="">Todos los Grupos</option>
                                            @foreach($grupos_list as $g)
                                            <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button class="btn btn-secondary-pastel" onclick="resetFilters('sub')">Limpiar filtros</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <div id="loading-sub" class="text-center p-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <table class="table table-hover mb-0" id="table-sub">
                                    <thead>
                                        <tr>
                                            <th class="sortable" onclick="sortTable('table-sub', 0)">Subgrupo</th>
                                            <th class="description-col">Descripción</th>
                                            <th class="sortable" onclick="sortTable('table-sub', 2)">Tipo</th>
                                            <th class="sortable" onclick="sortTable('table-sub', 3)">Línea</th>
                                            <th class="sortable" onclick="sortTable('table-sub', 4)">Grupo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subgrupos as $s)
                                        <tr data-nombre="{{ strtolower($s->nombre) }}" data-tipo="{{ $s->id_InvTipos }}" data-linea="{{ $s->id_InvLineas }}" data-grupo="{{ $s->id_InvGrupos }}">
                                            <td>{{ $s->nombre }}</td>
                                            <td class="description-cell">
                                                @if($s->descripcion)
                                                    <div class="description-container">
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal" 
                                                           data-title="Descripción de {{ $s->nombre }}" 
                                                           data-content="{{ $s->descripcion }}">
                                                            <span class="description-text">{{ $s->descripcion }}</span>
                                                            <i class="bi bi-eye ms-1"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><span class="badge bg-blue-pastel">{{ $s->tipo->nombre ?? '-' }}</span></td>
                                            <td><span class="badge bg-green-pastel">{{ $s->linea->nombre ?? '-' }}</span></td>
                                            <td><span class="badge bg-purple-pastel">{{ $s->grupo->nombre ?? '-' }}</span></td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary-pastel" onclick='editMain(@json($s))' title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    
                                                    <form action="{{ route('inventario.clasificacion.destroy', $s->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger-pastel" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-top" id="pagination-sub">
                                <div class="d-flex justify-content-center">{{ $subgrupos->links() }}</div>
                            </div>
                        </div>

                        {{-- 2. TABLA GRUPOS --}}
                        <div class="tab-pane fade" id="list-grupos" role="tabpanel" aria-labelledby="grupos-tab">
                            <!-- SECCIÓN DE FILTROS PARA GRUPOS -->
                            <div class="p-3 border-bottom bg-light-pastel">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="search-grupo" placeholder="Buscar...">
                                    </div>
                                    <div class="col-md-4">
                                        <select id="filter-linea-grupo" class="form-select">
                                            <option value="">Todas las Líneas</option>
                                            @foreach($lineas_list as $l)
                                            <option value="{{ $l->id }}">{{ $l->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-secondary-pastel w-100" onclick="resetFilters('grupo')">Limpiar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <div id="loading-grupo" class="text-center p-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <table class="table table-hover mb-0" id="table-grupo">
                                    <thead>
                                        <tr>
                                            <th class="sortable" onclick="sortTable('table-grupo', 0)">Grupo</th>
                                            <th class="description-col">Descripción</th>
                                            <th class="sortable" onclick="sortTable('table-grupo', 2)">Pertenece a Línea</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grupos_list as $g)
                                        <tr data-nombre="{{ strtolower($g->nombre) }}" data-linea="{{ $g->id_InvLineas }}">
                                            <td>{{ $g->nombre }}</td>
                                            <td class="description-cell">
                                                @if($g->descripcion)
                                                    <div class="description-container">
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal" 
                                                           data-title="Descripción de {{ $g->nombre }}" 
                                                           data-content="{{ $g->descripcion }}">
                                                            <span class="description-text">{{ $g->descripcion }}</span>
                                                            <i class="bi bi-eye ms-1"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><span class="badge bg-green-pastel">{{ $g->linea->nombre ?? 'Huérfano' }}</span></td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary-pastel" onclick='editParam("grupo", @json($g))' title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    
                                                    @if($g->subgrupos_count > 0)
                                                        <button class="btn btn-sm btn-secondary-pastel" title="No se puede borrar" disabled>
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $g->id, 'tipo' => 'grupo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger-pastel" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 3. TABLA LÍNEAS --}}
                        <div class="tab-pane fade" id="list-lineas" role="tabpanel" aria-labelledby="lineas-tab">
                            <!-- SECCIÓN DE FILTROS PARA LÍNEAS -->
                            <div class="p-3 border-bottom bg-light-pastel">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="search-linea" placeholder="Buscar...">
                                    </div>
                                    <div class="col-md-4">
                                        <select id="filter-tipo-linea" class="form-select">
                                            <option value="">Todos los Tipos</option>
                                            @foreach($tipos_list as $t)
                                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-secondary-pastel w-100" onclick="resetFilters('linea')">Limpiar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <div id="loading-linea" class="text-center p-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <table class="table table-hover mb-0" id="table-linea">
                                    <thead>
                                        <tr>
                                            <th class="sortable" onclick="sortTable('table-linea', 0)">Línea</th>
                                            <th class="description-col">Descripción</th>
                                            <th class="sortable" onclick="sortTable('table-linea', 2)">Pertenece a Tipo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lineas_list as $l)
                                        <tr data-nombre="{{ strtolower($l->nombre) }}" data-tipo="{{ $l->id_InvTipos }}">
                                            <td>{{ $l->nombre }}</td>
                                            <td class="description-cell">
                                                @if($l->descripcion)
                                                    <div class="description-container">
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal" 
                                                           data-title="Descripción de {{ $l->nombre }}" 
                                                           data-content="{{ $l->descripcion }}">
                                                            <span class="description-text">{{ $l->descripcion }}</span>
                                                            <i class="bi bi-eye ms-1"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td><span class="badge bg-blue-pastel">{{ $l->tipo->nombre ?? 'Huérfano' }}</span></td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary-pastel" onclick='editParam("linea", @json($l))' title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    
                                                    @if($l->grupos_count > 0 || $l->subgrupos_count > 0)
                                                        <button class="btn btn-sm btn-secondary-pastel" title="No se puede borrar" disabled>
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $l->id, 'tipo' => 'linea']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger-pastel" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 4. TABLA TIPOS --}}
                        <div class="tab-pane fade" id="list-tipos" role="tabpanel" aria-labelledby="tipos-tab">
                            <!-- SECCIÓN DE FILTROS PARA TIPOS -->
                            <div class="p-3 border-bottom bg-light-pastel">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="search-tipo" placeholder="Buscar...">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-secondary-pastel w-100" onclick="resetFilters('tipo')">Limpiar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <div id="loading-tipo" class="text-center p-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                                <table class="table table-hover mb-0" id="table-tipo">
                                    <thead>
                                        <tr>
                                            <th class="sortable" onclick="sortTable('table-tipo', 0)">Tipo Contable</th>
                                            <th class="description-col">Descripción</th>
                                            <th class="sortable" onclick="sortTable('table-tipo', 2)">Uso</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tipos_list as $t)
                                        <tr data-nombre="{{ strtolower($t->nombre) }}">
                                            <td>{{ $t->nombre }}</td>
                                            <td class="description-cell">
                                                @if($t->descripcion)
                                                    <div class="description-container">
                                                        <a href="#" class="description-link" data-bs-toggle="modal" data-bs-target="#descriptionModal" 
                                                           data-title="Descripción de {{ $t->nombre }}" 
                                                           data-content="{{ $t->descripcion }}">
                                                            <span class="description-text">{{ $t->descripcion }}</span>
                                                            <i class="bi bi-eye ms-1"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <span class="badge bg-blue-pastel">{{ $t->lineas_count }} Líneas</span>
                                                    <span class="badge bg-orange-pastel">{{ $t->subgrupos_count }} Subgrupos</span>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-primary-pastel" onclick='editParam("tipo", @json($t))' title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    
                                                    @if($t->lineas_count > 0 || $t->subgrupos_count > 0)
                                                        <button class="btn btn-sm btn-secondary-pastel" title="No se puede borrar" disabled>
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $t->id, 'tipo' => 'tipo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger-pastel" title="Eliminar"><i class="bi bi-trash"></i></button></form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar descripciones -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Descripción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="descriptionContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-pastel" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    
    
    <!-- ESTILOS PERSONALIZADOS -->
    <style>
        /* Paleta de colores pasteles corporativos */
        :root {
            --blue-pastel: #E3F2FD;
            --blue-pastel-border: #BBDEFB;
            --blue-pastel-text: #1976D2;
            
            --green-pastel: #E8F5E9;
            --green-pastel-border: #C8E6C9;
            --green-pastel-text: #388E3C;
            
            --purple-pastel: #F3E5F5;
            --purple-pastel-border: #E1BEE7;
            --purple-pastel-text: #7B1FA2;
            
            --orange-pastel: #FFF3E0;
            --orange-pastel-border: #FFE0B2;
            --orange-pastel-text: #F57C00;
            
            --pink-pastel: #FCE4EC;
            --pink-pastel-border: #F8BBD0;
            --pink-pastel-text: #C2185B;
            
            --light-pastel: #F5F5F5;
            --light-pastel-border: #EEEEEE;
        }
        
        .card {
            border: 1px solid #E0E0E0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .nav-tabs .nav-link {
            color: #757575;
            border: 1px solid transparent;
            border-bottom: none;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--blue-pastel-text);
            background-color: var(--blue-pastel);
            border-color: var(--blue-pastel-border) var(--blue-pastel-border) #fff;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--blue-pastel-text);
            border-color: var(--light-pastel-border) var(--light-pastel-border) #dee2e6;
        }
        
        /* Botones pasteles */
        .btn-primary-pastel {
            background-color: var(--blue-pastel);
            border-color: var(--blue-pastel-border);
            color: var(--blue-pastel-text);
            transition: all 0.3s ease;
        }
        
        .btn-primary-pastel:hover {
            background-color: var(--blue-pastel-border);
            border-color: var(--blue-pastel-text);
            color: var(--blue-pastel-text);
        }
        
        .btn-green-pastel {
            background-color: var(--green-pastel);
            border-color: var(--green-pastel-border);
            color: var(--green-pastel-text);
            transition: all 0.3s ease;
        }
        
        .btn-green-pastel:hover {
            background-color: var(--green-pastel-border);
            border-color: var(--green-pastel-text);
            color: var(--green-pastel-text);
        }
        
        .btn-purple-pastel {
            background-color: var(--purple-pastel);
            border-color: var(--purple-pastel-border);
            color: var(--purple-pastel-text);
            transition: all 0.3s ease;
        }
        
        .btn-purple-pastel:hover {
            background-color: var(--purple-pastel-border);
            border-color: var(--purple-pastel-text);
            color: var(--purple-pastel-text);
        }
        
        .btn-orange-pastel {
            background-color: var(--orange-pastel);
            border-color: var(--orange-pastel-border);
            color: var(--orange-pastel-text);
            transition: all 0.3s ease;
        }
        
        .btn-orange-pastel:hover {
            background-color: var(--orange-pastel-border);
            border-color: var(--orange-pastel-text);
            color: var(--orange-pastel-text);
        }
        
        .btn-danger-pastel {
            background-color: var(--pink-pastel);
            border-color: var(--pink-pastel-border);
            color: var(--pink-pastel-text);
            transition: all 0.3s ease;
        }
        
        .btn-danger-pastel:hover {
            background-color: var(--pink-pastel-border);
            border-color: var(--pink-pastel-text);
            color: var(--pink-pastel-text);
        }
        
        .btn-secondary-pastel {
            background-color: var(--light-pastel);
            border-color: var(--light-pastel-border);
            color: #616161;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-pastel:hover {
            background-color: var(--light-pastel-border);
            border-color: #BDBDBD;
            color: #424242;
        }
        
        /* Badges pasteles */
        .bg-blue-pastel {
            background-color: var(--blue-pastel) !important;
            color: var(--blue-pastel-text) !important;
        }
        
        .bg-green-pastel {
            background-color: var(--green-pastel) !important;
            color: var(--green-pastel-text) !important;
        }
        
        .bg-purple-pastel {
            background-color: var(--purple-pastel) !important;
            color: var(--purple-pastel-text) !important;
        }
        
        .bg-orange-pastel {
            background-color: var(--orange-pastel) !important;
            color: var(--orange-pastel-text) !important;
        }
        
        .bg-pink-pastel {
            background-color: var(--pink-pastel) !important;
            color: var(--pink-pastel-text) !important;
        }
        
        .bg-light-pastel {
            background-color: var(--light-pastel) !important;
        }
        
        /* Tabla */
        .table th {
            border-top: none;
            font-weight: 500;
            color: #616161;
            background-color: var(--light-pastel);
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--light-pastel);
        }
        
        .sortable {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.3s ease;
        }
        
        .sortable:hover {
            background-color: rgba(0,0,0,.03);
        }
        
        .sortable.asc::after {
            content: " ↑";
            color: var(--blue-pastel-text);
        }
        
        .sortable.desc::after {
            content: " ↓";
            color: var(--blue-pastel-text);
        }
        
        /* Formularios */
        .form-control:focus, .form-select:focus {
            border-color: var(--blue-pastel-border);
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.1);
        }
        
        .input-group .btn {
            border-left: none;
        }
        
        /* Estilos para modo edición */
        .editing {
            border: 2px solid var(--blue-pastel-border) !important;
            background-color: var(--blue-pastel) !important;
            box-shadow: 0 4px 8px rgba(25, 118, 210, 0.1) !important;
        }
    
        
        /* Paginación mejorada */
        .pagination .page-link {
            color: var(--blue-pastel-text);
            border-color: var(--blue-pastel-border);
            transition: all 0.3s ease;
        }
        
        .pagination .page-link:hover {
            background-color: var(--blue-pastel);
            border-color: var(--blue-pastel-text);
            color: var(--blue-pastel-text);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--blue-pastel);
            border-color: var(--blue-pastel-text);
            color: var(--blue-pastel-text);
        }
        
        /* Estilos para la columna de descripción */
        .description-col {
            width: 100px; /* Ancho fijo para la columna */
            max-width: 100px;
            min-width: 100px;
        }
        
        .description-cell {
            padding: 0.5rem;
            vertical-align: middle;
        }
        
        .description-container {
            width: 100%;
            overflow: hidden;
        }
        
        .description-link {
            color: var(--blue-pastel-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }
        
        .description-link:hover {
            text-decoration: underline;
            color: var(--blue-pastel-text);
        }
        
        .description-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70px; /* Deja espacio para el icono */
            display: inline-block;
        }
        
        .description-link i {
            font-size: 0.8em;
            flex-shrink: 0;
        }
        
    </style>

    {{-- LÓGICA JAVASCRIPT --}}
    <script>
        // Variable para controlar el timeout del debounce
        let filterTimeout;

        // --- 1. EDITAR VINCULACIÓN (PRINCIPAL) ---
        function editMain(data) {
            // 1. Preparar la interfaz para el modo de edición
            const mainTab = new bootstrap.Tab(document.getElementById('main-tab'));
            mainTab.show();
            
            document.getElementById('title-main').innerText = "Editando Subgrupo #" + data.id;
            document.getElementById('btnMain').innerHTML = 'Actualizar';
            document.getElementById('btnMain').className = "btn btn-primary-pastel w-100";
            document.getElementById('cancelMain').className = "btn btn-secondary-pastel w-100";
            
            // 2. Configurar el formulario para el método PUT (actualización)
            const form = document.getElementById('formMain');
            form.action = "{{ url('inventario/clasificacion') }}/" + data.id;
            document.getElementById('methodMain').value = "PUT";

            // 3. Llenar los campos del formulario
            document.getElementById('inpMainName').value = data.nombre;
            document.getElementById('inpMainDescripcion').value = data.descripcion || '';
            document.getElementById('selMainGrupo').value = data.id_InvGrupos;
            document.getElementById('selMainLinea').value = data.id_InvLineas;
            document.getElementById('selMainTipo').value = data.id_InvTipos;
        }

        function resetMain() {
            document.getElementById('title-main').innerText = "Nueva Vinculación";
            document.getElementById('btnMain').innerHTML = 'Guardar';
            document.getElementById('btnMain').className = "btn btn-primary-pastel w-100";
            document.getElementById('cancelMain').className = "btn btn-secondary-pastel w-100 d-none";
            
            // Limpiar el campo de descripción y su estado de edición
            const descripcionField = document.getElementById('inpMainDescripcion');
            descripcionField.value = '';
            descripcionField.dataset.originalValue = '';
            descripcionField.dataset.editing = 'false';
            
            document.getElementById('formMain').reset();
            document.getElementById('formMain').action = "{{ route('inventario.clasificacion.store') }}";
            document.getElementById('methodMain').value = "POST";
        }

        // --- 2. EDITAR CATÁLOGOS BASE (TIPO, LÍNEA, GRUPO) ---
        function editParam(type, data) {
            // Activar la pestaña de catálogos
            const catsTab = new bootstrap.Tab(document.getElementById('cats-tab'));
            catsTab.show();
            
            resetCats(); // Limpiar estados previos

            // Estilos visuales de "Editando"
            const box = document.getElementById('box-' + type);
            box.classList.add('editing');
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.getElementById('lbl-' + type).innerText = "EDITANDO #" + data.id;

            // Configurar Formulario
            const form = document.getElementById('form' + capitalize(type));
            form.action = "{{ url('inventario/clasificacion/update-') }}" + type + "/" + data.id;
            document.getElementById('method' + capitalize(type)).value = "PUT";

            // Llenar campos
            document.getElementById('inp' + capitalize(type) + 'Name').value = data.nombre;
            
            // Llenar campo de descripción
            const descField = document.getElementById('inp' + capitalize(type) + 'Descripcion');
            if (descField) {
                descField.value = data.descripcion || '';
            }
            
            // Llenar selects padre si aplica
            if(type === 'linea') document.getElementById('inpLineaParent').value = data.id_InvTipos;
            if(type === 'grupo') document.getElementById('inpGrupoParent').value = data.id_InvLineas;
        }

        function resetCats() {
            ['tipo', 'linea', 'grupo'].forEach(t => {
                // Quitar estilos
                document.getElementById('box-' + t).classList.remove('editing');
                document.getElementById('lbl-' + t).innerText = "NUEVO";
                
                // Resetear form y ruta
                const form = document.getElementById('form' + capitalize(t));
                form.reset();
                form.action = "{{ url('inventario/clasificacion/store-') }}" + t;
                document.getElementById('method' + capitalize(t)).value = "POST";
            });
        }

        function capitalize(str) { return str.charAt(0).toUpperCase() + str.slice(1); }

        // --- 3. FILTRADO AJAX CON DEBOUNCE ---
        function applyFiltersAjax(type) {
            // Limpiar el timeout anterior si existe
            clearTimeout(filterTimeout);
            
            // Establecer un nuevo timeout para evitar demasiadas peticiones
            filterTimeout = setTimeout(() => {
                let params = {};
                
                // Mostrar indicador de carga
                const loadingEl = document.getElementById(`loading-${type}`);
                if (loadingEl) {
                    loadingEl.style.display = 'block';
                }
                
                // Recopilar parámetros según el tipo
                switch(type) {
                    case 'sub':
                        params.search_sub = document.getElementById('search-subgrupo').value;
                        params.filter_tipo_sub = document.getElementById('filter-tipo-sub').value;
                        params.filter_linea_sub = document.getElementById('filter-linea-sub').value;
                        params.filter_grupo_sub = document.getElementById('filter-grupo-sub').value;
                        break;
                    case 'grupo':
                        params.search_grupo = document.getElementById('search-grupo').value;
                        params.filter_linea_grupo = document.getElementById('filter-linea-grupo').value;
                        break;
                    case 'linea':
                        params.search_linea = document.getElementById('search-linea').value;
                        params.filter_tipo_linea = document.getElementById('filter-tipo-linea').value;
                        break;
                    case 'tipo':
                        params.search_tipo = document.getElementById('search-tipo').value;
                        break;
                }
                
                // Realizar petición AJAX
                fetch(`{{ route('inventario.clasificacion.index') }}?${new URLSearchParams(params)}`)
                    .then(response => response.text())
                    .then(html => {
                        // Crear un elemento temporal para parsear el HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        
                        // Extraer la nueva tabla y paginación
                        const newTable = tempDiv.querySelector(`#table-${type} tbody`);
                        const newPagination = tempDiv.querySelector(`#pagination-${type} .d-flex`);
                        
                        // Actualizar la tabla
                        const currentTable = document.querySelector(`#table-${type} tbody`);
                        if (currentTable && newTable) {
                            currentTable.innerHTML = newTable.innerHTML;
                        }
                        
                        // Actualizar la paginación
                        const currentPagination = document.querySelector(`#pagination-${type} .d-flex`);
                        if (currentPagination && newPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                            // Re-asignar eventos a los nuevos enlaces de paginación
                            assignPaginationEvents(type);
                        }
                        
                        // Re-asignar eventos a los enlaces de descripción
                        assignDescriptionEvents();
                    })
                    .catch(error => {
                        console.error('Error al filtrar:', error);
                    })
                    .finally(() => {
                        // Ocultar indicador de carga
                        if (loadingEl) {
                            loadingEl.style.display = 'none';
                        }
                    });
            }, 300); // 300ms de debounce
        }

        // --- 4. ASIGNAR EVENTOS A PAGINACIÓN ---
        function assignPaginationEvents(type) {
            const paginationLinks = document.querySelectorAll(`#pagination-${type} a`);
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Mostrar indicador de carga
                    const loadingEl = document.getElementById(`loading-${type}`);
                    if (loadingEl) {
                        loadingEl.style.display = 'block';
                    }
                    
                    const url = this.getAttribute('href');
                    
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            // Crear un elemento temporal para parsear el HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;
                            
                            // Extraer la nueva tabla y paginación
                            const newTable = tempDiv.querySelector(`#table-${type} tbody`);
                            const newPagination = tempDiv.querySelector(`#pagination-${type} .d-flex`);
                            
                            // Actualizar la tabla
                            const currentTable = document.querySelector(`#table-${type} tbody`);
                            if (currentTable && newTable) {
                                currentTable.innerHTML = newTable.innerHTML;
                            }
                            
                            // Actualizar la paginación
                            const currentPagination = document.querySelector(`#pagination-${type} .d-flex`);
                            if (currentPagination && newPagination) {
                                currentPagination.innerHTML = newPagination.innerHTML;
                                // Re-asignar eventos recursivamente
                                assignPaginationEvents(type);
                            }
                            
                            // Re-asignar eventos a los enlaces de descripción
                            assignDescriptionEvents();
                        })
                        .catch(error => {
                            console.error('Error en paginación:', error);
                        })
                        .finally(() => {
                            // Ocultar indicador de carga
                            if (loadingEl) {
                                loadingEl.style.display = 'none';
                            }
                        });
                });
            });
        }

        function resetFilters(type) {
            let searchId, filterIds = [];
            
            switch(type) {
                case 'sub':
                    searchId = 'search-subgrupo';
                    filterIds = ['filter-tipo-sub', 'filter-linea-sub', 'filter-grupo-sub'];
                    break;
                case 'grupo':
                    searchId = 'search-grupo';
                    filterIds = ['filter-linea-grupo'];
                    break;
                case 'linea':
                    searchId = 'search-linea';
                    filterIds = ['filter-tipo-linea'];
                    break;
                case 'tipo':
                    searchId = 'search-tipo';
                    break;
            }
            
            // Limpiar campo de búsqueda
            document.getElementById(searchId).value = '';
            
            // Limpiar selects
            filterIds.forEach(id => {
                if (document.getElementById(id)) {
                    document.getElementById(id).value = '';
                }
            });
            
            // Aplicar filtros para recargar todos los datos
            applyFiltersAjax(type);
        }

        // --- 5. FUNCIONES DE ORDENAMIENTO ---
        // Estado de ordenamiento para cada tabla
        const sortDirections = {};

        // Función de ordenamiento
        function sortTable(tableId, columnIndex) {
            // Obtener la tabla y el cuerpo
            const table = document.getElementById(tableId);
            if (!table) return;
            
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const headers = table.querySelectorAll('th');
            
            // Determinar la dirección de ordenamiento
            if (!sortDirections[tableId]) {
                sortDirections[tableId] = {};
            }
            
            const currentDirection = sortDirections[tableId][columnIndex] || 'asc';
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            sortDirections[tableId][columnIndex] = newDirection;
            
            // Actualizar clases visuales en los headers
            headers.forEach((header, index) => {
                header.classList.remove('asc', 'desc');
                if (index === columnIndex) {
                    header.classList.add(newDirection);
                }
            });
            
            // Función para obtener el valor de una celda
            const getCellValue = (row, colIndex) => {
                const cell = row.cells[colIndex];
                if (!cell) return '';
                
                // Para otras columnas, devolver el texto
                return cell.textContent.trim().toLowerCase();
            };
            
            // Ordenar las filas
            rows.sort((a, b) => {
                const aValue = getCellValue(a, columnIndex);
                const bValue = getCellValue(b, columnIndex);
                
                // Comparación de texto
                if (aValue < bValue) {
                    return newDirection === 'asc' ? -1 : 1;
                }
                if (aValue > bValue) {
                    return newDirection === 'asc' ? 1 : -1;
                }
                return 0;
            });
            
            // Volver a agregar las filas ordenadas a la tabla con animación
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                tbody.appendChild(row);
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 20);
            });
        }

        // --- 6. MANEJO DE MODAL DE DESCRIPCIÓN ---
        function assignDescriptionEvents() {
            const descriptionLinks = document.querySelectorAll('.description-link');
            
            descriptionLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const title = this.getAttribute('data-title');
                    const content = this.getAttribute('data-content');
                    
                    document.getElementById('descriptionModalLabel').textContent = title;
                    document.getElementById('descriptionContent').textContent = content;
                });
            });
        }

        // --- 7. INICIALIZACIÓN DE EVENTOS ---
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- SECCIÓN A: FILTROS Y BÚSQUEDA AJAX ---
            // Configurar eventos para filtros de subgrupos
            const searchSubgrupo = document.getElementById('search-subgrupo');
            const filterTipoSub = document.getElementById('filter-tipo-sub');
            const filterLineaSub = document.getElementById('filter-linea-sub');
            const filterGrupoSub = document.getElementById('filter-grupo-sub');
            
            if (searchSubgrupo) {
                searchSubgrupo.addEventListener('input', () => applyFiltersAjax('sub'));
            }
            if (filterTipoSub) {
                filterTipoSub.addEventListener('change', () => applyFiltersAjax('sub'));
            }
            if (filterLineaSub) {
                filterLineaSub.addEventListener('change', () => applyFiltersAjax('sub'));
            }
            if (filterGrupoSub) {
                filterGrupoSub.addEventListener('change', () => applyFiltersAjax('sub'));
            }
            
            // Configurar eventos para filtros de grupos
            const searchGrupo = document.getElementById('search-grupo');
            const filterLineaGrupo = document.getElementById('filter-linea-grupo');
            
            if (searchGrupo) {
                searchGrupo.addEventListener('input', () => applyFiltersAjax('grupo'));
            }
            if (filterLineaGrupo) {
                filterLineaGrupo.addEventListener('change', () => applyFiltersAjax('grupo'));
            }
            
            // Configurar eventos para filtros de líneas
            const searchLinea = document.getElementById('search-linea');
            const filterTipoLinea = document.getElementById('filter-tipo-linea');
            
            if (searchLinea) {
                searchLinea.addEventListener('input', () => applyFiltersAjax('linea'));
            }
            if (filterTipoLinea) {
                filterTipoLinea.addEventListener('change', () => applyFiltersAjax('linea'));
            }
            
            // Configurar eventos para filtros de tipos
            const searchTipo = document.getElementById('search-tipo');
            
            if (searchTipo) {
                searchTipo.addEventListener('input', () => applyFiltersAjax('tipo'));
            }
            
            // Asignar eventos a la paginación inicial
            ['sub', 'grupo', 'linea', 'tipo'].forEach(type => {
                assignPaginationEvents(type);
            });
            
            // Asignar eventos a los enlaces de descripción
            assignDescriptionEvents();

            // --- SECCIÓN B: LÓGICA DE SELECTS EN CASCADA (TIPO -> LÍNEA -> GRUPO) ---
            const selMainTipo = document.getElementById('selMainTipo');
            const selMainLinea = document.getElementById('selMainLinea');
            const selMainGrupo = document.getElementById('selMainGrupo');
            
            if (selMainTipo && selMainLinea && selMainGrupo) {
                // Guardamos TODAS las opciones originales al cargar la página
                const allLineaOptions = Array.from(selMainLinea.querySelectorAll('option'));
                const allGrupoOptions = Array.from(selMainGrupo.querySelectorAll('option'));

                // --- EVENTO 1: Cuando cambia el TIPO ---
                selMainTipo.addEventListener('change', function() {
                    const selectedTipoId = this.value;
                    
                    // Guardar el valor actual del campo de descripción antes de limpiar los selects
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    const currentDescripcion = descripcionField ? descripcionField.value : '';
                    
                    // Limpiamos y reseteamos el select de LÍNEA
                    selMainLinea.innerHTML = '';
                    selMainLinea.readonly = true;

                    // Limpiamos y reseteamos el select de GRUPO (porque si no hay línea, no hay grupo)
                    selMainGrupo.innerHTML = '<option value="">Seleccione primero una Línea...</option>';
                    selMainGrupo.readonly = true;

                    if (selectedTipoId === '') {
                        // Si no hay tipo seleccionado, mostramos el mensaje por defecto
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Seleccione primero un Tipo...';
                        selMainLinea.appendChild(option);
                    } else {
                        // Filtramos las opciones de LÍNEA que pertenecen al TIPO seleccionado
                        const filteredLineas = allLineaOptions.filter(option => {
                            return option.value === '' || option.dataset.tipo === selectedTipoId;
                        });
                        
                        // Agregamos las opciones filtradas al select de LÍNEA
                        filteredLineas.forEach(option => {
                            selMainLinea.appendChild(option);
                        });
                        
                        // Habilitamos el select de LÍNEA
                        selMainLinea.readonly = false;
                    }
                    
                    // Restaurar el valor del campo de descripción solo si no estamos en modo edición
                    if (descripcionField && descripcionField.dataset.editing !== 'true') {
                        descripcionField.value = currentDescripcion;
                    }
                });

                // --- EVENTO 2: Cuando cambia la LÍNEA ---
                selMainLinea.addEventListener('change', function() {
                    const selectedLineaId = this.value;
                    
                    // Guardar el valor actual del campo de descripción antes de limpiar los selects
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    const currentDescripcion = descripcionField ? descripcionField.value : '';
                    
                    // Limpiamos el select de GRUPO
                    selMainGrupo.innerHTML = '';
                    
                    if (selectedLineaId === '') {
                        // Si no hay línea seleccionada, mostramos el mensaje y deshabilitamos
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Seleccione primero una Línea...';
                        selMainGrupo.appendChild(option);
                        selMainGrupo.disabled = true;
                    } else {
                        // Filtramos las opciones de GRUPO que pertenecen a la LÍNEA seleccionada
                        const filteredGrupos = allGrupoOptions.filter(option => {
                            return option.value === '' || option.dataset.linea === selectedLineaId;
                        });
                        
                        // Agregamos las opciones filtradas al select de GRUPO
                        filteredGrupos.forEach(option => {
                            selMainGrupo.appendChild(option);
                        });
                        
                        // Habilitamos el select de GRUPO
                        selMainGrupo.disabled = false;
                    }
                    
                    // Restaurar el valor del campo de descripción solo si no estamos en modo edición
                    if (descripcionField && descripcionField.dataset.editing !== 'true') {
                        descripcionField.value = currentDescripcion;
                    }
                });
                
                // ESTADO INICIAL: Si la página carga sin un tipo preseleccionado, deshabilitamos los campos hijos
                if (selMainTipo.value === '') {
                    selMainLinea.readonly = true;
                    selMainGrupo.readonly = true;
                }
            }

            // --- SECCIÓN C: PROTECCIÓN DEL CAMPO DESCRIPCIÓN ---
            const inpMainDescripcion = document.getElementById('inpMainDescripcion');
            if (inpMainDescripcion) {
                // Guardar el valor actual del campo cuando el usuario hace foco en él
                inpMainDescripcion.addEventListener('focus', function() {
                    this.dataset.currentValue = this.value;
                });
                
                // Restaurar el valor si se pierde al salir del campo (por ejemplo, si un script lo limpia)
                inpMainDescripcion.addEventListener('blur', function() {
                    if (!this.value && this.dataset.currentValue) {
                        this.value = this.dataset.currentValue;
                    }
                });
                
                // Prevenir que el campo se limpie al cambiar los selects
                inpMainDescripcion.addEventListener('change', function() {
                    this.dataset.originalValue = this.value;
                });
            }
            
            // --- SECCIÓN D: ASEGURAR QUE EL FORMULARIO ENVÍE EL VALOR DE DESCRIPCIÓN ---
            const formMain = document.getElementById('formMain');
            if (formMain) {
                formMain.addEventListener('submit', function(e) {
                    const descripcionField = document.getElementById('inpMainDescripcion');
                    if (descripcionField) {
                        // Asegurarnos de que el campo tenga un valor antes de enviar el formulario
                        if (!descripcionField.value && descripcionField.dataset.originalValue) {
                            descripcionField.value = descripcionField.dataset.originalValue;
                        }
                        
                        // Guardar el valor actual como valor original para futuros usos
                        descripcionField.dataset.originalValue = descripcionField.value;
                        
                        // Marcar que ya no estamos en modo edición
                        descripcionField.dataset.editing = 'false';
                    }
                });
            }
        });
    </script>
</x-base-layout>