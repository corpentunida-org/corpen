<x-base-layout>
    @section('titlepage', 'Perfil de Usuario')
    
    <x-success />
    <x-error />

    <style>
        /* =========================================
           SISTEMA DE DISEÑO UI CORPORATIVO PASTEL
           ========================================= */
        
        /* Tipografía y Contenedores */
        .ui-card {
            background: #ffffff;
            border: 1px solid #eaedf1;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        /* Banner superior del perfil con gradiente pastel */
        .ui-profile-banner {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            height: 110px; /* Ligeramente más compacto */
            width: 100%;
            position: relative;
        }
        
        /* Avatar superpuesto */
        .ui-avatar-wrapper {
            margin-top: -55px;
            position: relative;
            display: inline-flex;
            justify-content: center;
        }
        .ui-avatar-image {
            width: 120px; 
            height: 120px;
            border: 5px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            object-fit: cover;
            background-color: #fff;
        }
        .ui-verified-badge {
            position: absolute;
            bottom: 2px;
            right: 2px;
            background: #ffffff;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Cajas de estadísticas (Stats) */
        .ui-stat-box {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 1.15rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .ui-stat-box:hover {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transform: translateY(-2px);
        }
        .ui-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        /* Paleta Pastel para Iconos */
        .ui-pastel-blue { background: #e0f2fe; color: #0284c7; }
        .ui-pastel-purple { background: #f3e8ff; color: #9333ea; }
        .ui-pastel-amber { background: #fef3c7; color: #d97706; }
        .ui-pastel-green { background: #dcfce7; color: #16a34a; }

        /* Gráfico de Barras CSS (Sin Librerías) */
        .ui-chart-container {
            height: 70px;
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin-top: 10px;
        }
        .ui-chart-bar {
            background-color: #bae6fd; /* Azul pastel base */
            border-radius: 4px 4px 0 0;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        .ui-chart-bar:hover { background-color: #38bdf8; }
        .ui-chart-bar.active { background-color: #0ea5e9; } /* Día actual */

        /* Formularios Modernos (Inputs) */
        .ui-form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ui-input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1.15rem;
            background: #f8fafc;
            font-size: 0.95rem;
            color: #1e293b;
            transition: all 0.2s;
            width: 100%;
        }
        .ui-input:focus {
            background: #ffffff;
            border-color: #8ec5fc;
            box-shadow: 0 0 0 4px rgba(142, 197, 252, 0.2);
            outline: none;
        }
        
        /* Acordeón de Permisos (Premium Look) */
        .ui-accordion .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px !important;
            margin-bottom: 0.85rem;
            overflow: hidden;
            background: #ffffff;
        }
        .ui-accordion .accordion-button {
            background: #ffffff;
            font-weight: 600;
            color: #1e293b;
            padding: 1.15rem 1.5rem;
            box-shadow: none !important;
        }
        .ui-accordion .accordion-button:not(.collapsed) {
            background: #f8fafc;
            color: #6366f1;
            border-bottom: 1px solid #e2e8f0;
        }
        .ui-accordion .accordion-button::after { filter: contrast(0.5); }
        
        /* Switches (Toggles estilo iOS/Mac) */
        .ui-switch .form-check-input {
            height: 1.5rem; width: 2.75rem; border-radius: 2rem; cursor: pointer;
            border-color: #cbd5e1; background-color: #e2e8f0;
        }
        .ui-switch .form-check-input:checked { background-color: #6366f1; border-color: #6366f1; }
        .ui-switch .form-check-label {
            padding-top: 0.2rem; padding-left: 0.5rem; font-size: 0.9rem;
            font-weight: 500; color: #334155; cursor: pointer;
        }

        /* Danger Zone */
        .ui-danger-zone {
            border: 1px solid #fecaca; background: #fff5f5;
            border-radius: 12px; padding: 1.5rem;
        }
        
        /* Botones Generales */
        .ui-btn-danger {
            background: #ef4444; color: #fff; border: none; border-radius: 8px;
            padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.2s;
        }
        .ui-btn-danger:hover { background: #dc2626; transform: translateY(-1px); }
        
        .ui-btn-primary {
            background: #4f46e5; color: #fff; border: none; border-radius: 8px;
            padding: 0.75rem 2rem; font-weight: 600; transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        .ui-btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3); }

        /* Botón de Retorno (Espaciado Corregido) */
        .ui-back-link {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 0.5rem; /* Margen drásticamente reducido */
            padding: 0.25rem 0;
            font-size: 0.95rem;
        }
        .ui-back-link:hover { color: #4f46e5; transform: translateX(-4px); }
        
        /* Estilo adicional para los links de navegación */
        .ui-nav-item {
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
            padding: 0.5rem 0;
            font-size: 0.95rem;
        }
        .ui-nav-item:hover {
            color: #4f46e5;
            transform: translateY(-2px);
        }
    </style>

    <div class="container-fluid px-0">
        
        <!-- BOTÓN DE VOLVER AL INDEX (Con margen reducido para matar el espacio en blanco) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="ui-back-link mb-0">
                    <i class="bi bi-people fs-6 me-2"></i> Volver al listado de usuarios
                </a>
                <a href="{{ route('admin.roles.index') }}" class="ui-nav-item mb-0">
                    <i class="bi bi-shield-check fs-6 me-2"></i> Ver Roles y Permisos
                </a>
            </div>
        </div>
    

        <div class="row g-4 mb-5">
            
            <!-- ==========================================
                 COLUMNA IZQUIERDA: PERFIL, KPIs Y GRÁFICO
                 ========================================== -->
            <div class="col-xl-4 col-lg-5">
                <div class="ui-card h-100">
                    <div class="ui-profile-banner"></div>
                    
                    <div class="card-body px-4 pb-4 text-center">
                        
                        <!-- Avatar -->
                        <div class="ui-avatar-wrapper w-100">
                            <div class="position-relative d-inline-block">
                                @if (isset($user->ubicacion_foto) && $user->ubicacion_foto)
                                    <img src="{{ route('archivo.empleado.verFoto', $user->id) }}" alt="Avatar" class="ui-avatar-image">
                                @else
                                    @php
                                        $nombres = explode(' ', trim($user->name));
                                        $iniciales = strtoupper(substr($nombres[0] ?? 'U', 0, 1) . substr($nombres[1] ?? '', 0, 1));
                                    @endphp
                                    <div class="ui-avatar-image d-flex align-items-center justify-content-center fw-bold text-primary" style="background-color: #e0f2fe; font-size: 2.5rem;">
                                        {{ $iniciales }}
                                    </div>
                                @endif
                                <div class="ui-verified-badge text-primary">
                                    <i class="bi bi-patch-check-fill fs-6"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Básica -->
                        <div class="mt-3 mb-4">
                            <h4 class="fw-bold text-dark mb-1 fs-5">{{ $user->name }}</h4>
                            <span class="badge bg-light text-secondary border border-light-subtle px-3 py-2 rounded-pill fs-13">
                                <i class="bi bi-envelope me-2"></i>{{ $user->email }}
                            </span>
                        </div>

                        <!-- Panel de KPIs y Gráfico -->
                        <div class="d-flex flex-column gap-3 text-start">
                            
                            <!-- KPI 1: Rol Principal -->
                            <div class="ui-stat-box">
                                <div class="ui-icon-box ui-pastel-purple">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <div>
                                    <p class="fs-12 text-muted text-uppercase fw-bold mb-1 tracking-wide">Rol Principal Asignado</p>
                                    <h6 class="fw-bolder mb-0 text-dark">
                                        {{ strtoupper($user->actions->first()?->role?->name ?? 'SIN ROL') }}
                                    </h6>
                                </div>
                            </div>

                            <!-- KPI 2: Permisos -->
                            <div class="ui-stat-box">
                                <div class="ui-icon-box ui-pastel-green">
                                    <i class="bi bi-ui-checks"></i>
                                </div>
                                <div>
                                    <p class="fs-12 text-muted text-uppercase fw-bold mb-1 tracking-wide">Permisos Habilitados</p>
                                    <h6 class="fw-bolder mb-0 text-dark">
                                        {{ isset($permisosAsignados) ? count($permisosAsignados) : 0 }} 
                                        <span class="fs-13 fw-normal text-muted">accesos activos</span>
                                    </h6>
                                </div>
                            </div>

                            <!-- KPI 3: Actividad & Gráfico CSS (NUEVO) -->
                            <div class="ui-stat-box flex-column align-items-stretch">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <p class="fs-12 text-muted text-uppercase fw-bold mb-1 tracking-wide">Actividad Reciente</p>
                                        <h6 class="fw-bolder mb-0 text-dark">{{ $acciones }} <span class="fs-13 fw-normal text-muted">interacciones</span></h6>
                                    </div>
                                    <div class="ui-icon-box ui-pastel-blue bg-transparent" style="width: 32px; height: 32px;">
                                        <i class="bi bi-graph-up-arrow fs-5"></i>
                                    </div>
                                </div>
                                
                                <!-- Gráfico de Barras Pure CSS -->
                                <div class="ui-chart-container">
                                    <div class="ui-chart-bar" style="height: 35%;" data-bs-toggle="tooltip" title="Lunes: Baja"></div>
                                    <div class="ui-chart-bar" style="height: 60%;" data-bs-toggle="tooltip" title="Martes: Media"></div>
                                    <div class="ui-chart-bar" style="height: 20%;" data-bs-toggle="tooltip" title="Miércoles: Muy Baja"></div>
                                    <div class="ui-chart-bar" style="height: 85%;" data-bs-toggle="tooltip" title="Jueves: Alta"></div>
                                    <div class="ui-chart-bar" style="height: 50%;" data-bs-toggle="tooltip" title="Viernes: Media"></div>
                                    <div class="ui-chart-bar" style="height: 15%;" data-bs-toggle="tooltip" title="Sábado: Mínima"></div>
                                    <div class="ui-chart-bar active" style="height: 100%;" data-bs-toggle="tooltip" title="Hoy: Máxima"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2 text-muted px-1" style="font-size: 0.65rem; font-weight: 700;">
                                    <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span class="text-primary">D</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 COLUMNA DERECHA: FORMULARIOS Y ROLES
                 ========================================== -->
            <div class="col-xl-8 col-lg-7">
                
                <form method="POST" action="{{ route('admin.users.update', $user) }}" id="formUpdateUser" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <!-- Tarjeta: Datos Personales -->
                    <div class="ui-card mb-4">
                        <div class="card-header bg-white border-bottom border-light-subtle p-3 p-md-4">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-person-vcard me-2 text-primary"></i>Datos Personales</h6>
                            <p class="text-muted fs-13 mb-0">Gestione la información de contacto y credenciales.</p>
                        </div>
                        
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="ui-form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="ui-input" value="{{ $user->name }}" name="name" required placeholder="Ej. Carlos Mendoza">
                                </div>
                                <div class="col-md-6">
                                    <label class="ui-form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="ui-input" value="{{ $user->email }}" name="email" required placeholder="correo@empresa.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="ui-form-label">Teléfono de Contacto</label>
                                    <input type="text" class="ui-input" value="+01 (375) 2589 645" placeholder="+00 (000) 0000 000">
                                </div>
                                <div class="col-md-6">
                                    <label class="ui-form-label">Actualizar Contraseña</label>
                                    <input type="password" class="ui-input" name="pass" placeholder="Dejar en blanco para mantener actual">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta: Asignar Nuevo Rol -->
                    <div class="ui-card mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="ui-icon-box ui-pastel-blue me-3" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="bi bi-plus-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-15">Vincular Perfil Adicional</h6>
                                    <p class="text-muted fs-13 mb-0 mt-1">Expande los privilegios operativos del usuario.</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <select class="form-select ui-input cursor-pointer" name="rolnuevo">
                                        <option value="" disabled selected>Seleccione un perfil de la lista...</option>
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->id }}" {{ $user->actions->first()?->role?->id === $rol->id ? 'selected' : '' }}>
                                                {{ strtoupper($rol->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Encabezado Matriz de Roles -->
                    <div class="d-flex justify-content-between align-items-end mb-3 mt-4 px-1">
                        <div>
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-sliders me-2 text-primary"></i>Roles Activos & Permisos</h6>
                            <p class="text-muted fs-13 mb-0">Personaliza los permisos dentro de cada rol asignado.</p>
                        </div>
                        <span class="badge ui-pastel-purple rounded-pill px-3 py-2 fs-12 shadow-sm border border-light">
                            {{ count($user->actions) }} Rol(es) Activo(s)
                        </span>
                    </div>

                    <!-- Acordeón de Permisos -->
                    <div class="accordion ui-accordion mb-4" id="accordionRolesPermissions">
                        @forelse ($user->actions as $index => $rol)
                            <div class="accordion-item shadow-sm">
                                <h2 class="accordion-header" id="heading-{{ $index }}">
                                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" 
                                            type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse-{{ $index }}" 
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                        <i class="bi bi-shield-check text-success me-3 fs-5"></i> 
                                        PERFIL: {{ strtoupper($rol->role->name) }}
                                    </button>
                                </h2>
                                <div id="collapse-{{ $index }}" 
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                     data-bs-parent="#accordionRolesPermissions">
                                    <div class="accordion-body p-3 p-md-4 bg-light">
                                        
                                        <div class="mb-3 pb-3 border-bottom border-light-subtle">
                                            <h6 class="fw-bold text-dark fs-14">Matriz de Configuración</h6>
                                        </div>
                                        
                                        <div class="row g-3">
                                            @foreach ($permisosUsuario->where('role_id', $rol->role_id) as $permiso)
                                                <div class="col-lg-6 col-xl-4">
                                                    <div class="form-check form-switch ui-switch d-flex align-items-center">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                                                            class="form-check-input flex-shrink-0" id="perm_{{ $rol->role_id }}_{{ $permiso->id }}"
                                                            @if (in_array($permiso->id, $permisosAsignados)) checked @endif>
                                                        <label class="form-check-label user-select-none text-truncate" for="perm_{{ $rol->role_id }}_{{ $permiso->id }}" style="font-size: 0.85rem;" title="{{ $permiso->name }}">
                                                            {{ $permiso->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ui-card p-4 text-center bg-white shadow-sm border-dashed">
                                <div class="ui-icon-box bg-light text-muted mx-auto mb-2" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Sin Roles Asignados</h6>
                                <p class="text-muted fs-13 mb-0">Este usuario no cuenta con perfiles operativos activos.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Botonera de Guardado -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center bg-white p-3 p-md-4 rounded-4 border border-light-subtle shadow-sm mb-4">
                        <a href="#cardAddPermisos" class="text-danger fw-semibold text-decoration-none px-2 mb-3 mb-sm-0 fs-14 d-flex align-items-center">
                            <i class="bi bi-arrow-down-circle me-2"></i> Ir a Zona de Peligro
                        </a>
                        <button type="submit" class="ui-btn-primary w-100 w-sm-auto">
                            <i class="bi bi-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>

                <!-- Zona de Peligro -->
                <div class="ui-danger-zone shadow-sm" id="cardAddPermisos">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <h6 class="fw-bold text-danger mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Revocar Perfil
                            </h6>
                            <p class="text-danger opacity-75 fs-13 mb-0 pe-md-3">
                                Acción irreversible. El usuario perderá acceso a los módulos de inmediato.
                            </p>
                        </div>
                        
                        <div class="col-lg-6 border-start border-danger border-opacity-25 ps-lg-3">
                            <form method="POST" action="{{ route('admin.roles.destroy', $user->id) }}" id="formAddPermiso" novalidate>
                                @csrf
                                @method('DELETE')
                                
                                <div class="d-flex flex-column flex-sm-row gap-2 mt-1">
                                    <select class="form-select flex-grow-1 border-danger border-opacity-50 text-danger bg-white shadow-none fs-14" name="rol" required>
                                        <option value="" disabled selected>Seleccione rol a revocar...</option>
                                        @foreach ($user->actions as $rol)
                                            <option value="{{ $rol->role_id }}">
                                                {{ strtoupper($rol->role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    <button class="ui-btn-danger text-nowrap fs-14" data-bs-toggle="tooltip" title="Revocar acceso" type="submit">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar tooltips para el gráfico de actividad y botones
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Validación de formulario
            $('#formUpdateUser, #formAddPermiso').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    </script>
</x-base-layout>