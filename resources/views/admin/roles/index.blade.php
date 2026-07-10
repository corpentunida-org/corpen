<x-base-layout>
    @section('titlepage', 'Administración de Roles')
    
    <x-success />
    <x-error />

    <style>
        /* =========================================
           SISTEMA DE DISEÑO UI (Basado en tu referencia)
           ========================================= */
        
        /* Contenedores Principales */
        .ui-card {
            background: #ffffff;
            border: 1px solid #eaedf1;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        /* Banner superior con gradiente pastel */
        .ui-banner {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(142, 197, 252, 0.2);
        }
        .ui-banner::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        /* Cajas de Iconos (Paleta Pastel) */
        .ui-icon-box {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; flex-shrink: 0;
        }
        .ui-pastel-blue { background: #e0f2fe; color: #0284c7; }
        .ui-pastel-purple { background: #f3e8ff; color: #9333ea; }
        .ui-pastel-amber { background: #fef3c7; color: #d97706; }
        .ui-pastel-rose { background: #ffe4e6; color: #e11d48; }
        .ui-pastel-green { background: #dcfce7; color: #16a34a; }

        /* Formularios Modernos */
        .ui-form-label {
            font-size: 0.85rem; font-weight: 600; color: #475569;
            margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;
            display: block;
        }
        .ui-input {
            border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 0.85rem 1.25rem; background: #f8fafc;
            font-size: 0.95rem; color: #1e293b; transition: all 0.2s;
            width: 100%;
        }
        .ui-input:focus {
            background: #ffffff; border-color: #8ec5fc;
            box-shadow: 0 0 0 4px rgba(142, 197, 252, 0.2); outline: none;
        }
        
        /* Botones Premium */
        .ui-btn-primary {
            background: #4f46e5; color: #fff; border: none;
            border-radius: 8px; padding: 0.85rem 2rem;
            font-weight: 600; transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .ui-btn-primary:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3); color: #fff;}
        .ui-btn-white {
            background: #ffffff; color: #4f46e5; border: none;
            border-radius: 8px; padding: 0.85rem 2rem;
            font-weight: 700; transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .ui-btn-white:hover { background: #f8fafc; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1); color: #4338ca;}

        /* Acordeón de Permisos (Premium Look) */
        .ui-accordion .accordion-item {
            border: 1px solid #e2e8f0; border-radius: 12px !important;
            margin-bottom: 1rem; overflow: hidden; background: #ffffff;
        }
        .ui-accordion .accordion-button {
            background: #ffffff; font-weight: 600; color: #1e293b;
            padding: 1.25rem 1.5rem; box-shadow: none !important;
        }
        .ui-accordion .accordion-button:not(.collapsed) {
            background: #f8fafc; color: #6366f1;
            border-bottom: 1px solid #e2e8f0;
        }
        .ui-accordion .accordion-button::after { filter: contrast(0.5); }

        /* Switches (Toggles estilo iOS/Mac) */
        .ui-switch .form-check-input {
            height: 1.5rem; width: 2.75rem; border-radius: 2rem;
            cursor: pointer; border-color: #cbd5e1; background-color: #e2e8f0;
        }
        .ui-switch .form-check-input:checked {
            background-color: #6366f1; border-color: #6366f1;
        }
        .ui-switch .form-check-label {
            padding-top: 0.2rem; padding-left: 0.5rem;
            font-size: 0.9rem; font-weight: 500; color: #334155; cursor: pointer;
        }
    </style>

    <div class="container-fluid px-0 pb-5">
        <!-- BOTÓN DE VOLVER AL INDEX (Con margen reducido para matar el espacio en blanco) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="ui-back-link mb-0">
                    <i class="bi bi-people fs-6 me-2"></i> Listado de usuarios
                </a>
            </div>
        </div>
    
        <div class="ui-banner mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
            <div class="position-relative z-1 text-white">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center backdrop-blur" style="width: 56px; height: 56px;">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                    <h2 class="fw-bolder mb-0 fs-2">Gestión de Roles</h2>
                </div>
                <p class="mb-0 fs-15 opacity-75 fw-medium ms-1">Configuración centralizada de perfiles, accesos y permisos operativos.</p>
            </div>
            <div class="position-relative z-1 d-flex flex-column flex-sm-row gap-3">
                <a href="#cardAddRole" class="ui-btn-white text-decoration-none" id="mostrarCardRole">
                    <i class="bi bi-plus-lg"></i> Crear Rol
                </a>
                <a href="#cardAddPermisos" class="ui-btn-primary text-decoration-none border border-white border-opacity-25" id="mostrarCardPermisos">
                    <i class="bi bi-key-fill"></i> Crear Permiso
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-12" id="cardAddRole" style="display: none;">
                <div class="ui-card">
                    <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ui-icon-box ui-pastel-rose shadow-sm" style="width: 40px; height: 40px;">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-16">Nuevo Perfil de Rol</h5>
                                <p class="text-muted fs-13 mb-0 mt-1">Define una nueva agrupación lógica de permisos.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-cerrar-form shadow-none" aria-label="Cerrar"></button>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('admin.roles.store') }}" id="formAddRole" novalidate>
                            @csrf
                            @method('POST')
                            
                            <div class="row align-items-end g-4">
                                <div class="col-lg-8">
                                    <label class="ui-form-label">Nombre del Rol <span class="text-danger">*</span></label>
                                    <input type="text" class="ui-input" name="namerole" placeholder="Ej. Analista Financiero, Soporte..." required>
                                </div>
                                <div class="col-lg-4 text-lg-end">
                                    <button class="ui-btn-primary w-100 w-lg-auto" type="submit">
                                        <i class="bi bi-save"></i> Guardar Registro
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12" id="cardAddPermisos" style="display: none;">
                <div class="ui-card">
                    <div class="card-header bg-white border-bottom border-light-subtle p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ui-icon-box ui-pastel-amber shadow-sm" style="width: 40px; height: 40px;">
                                <i class="bi bi-unlock-fill"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-16">Registrar Nuevo Permiso</h5>
                                <p class="text-muted fs-13 mb-0 mt-1">Crea una regla técnica y asígnala a un rol existente.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-cerrar-form shadow-none" aria-label="Cerrar"></button>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form method="POST" action="{{ route('admin.permisos.store') }}" id="formAddPermiso" novalidate>
                            @csrf
                            @method('POST')
                            
                            <div class="row align-items-end g-4">
                                <div class="col-md-5">
                                    <label class="ui-form-label">Clave Técnica (Nomenclatura) <span class="text-danger">*</span></label>
                                    <input type="text" class="ui-input text-uppercase font-monospace text-primary" name="permisoName" placeholder="MODULO.ACCION" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="ui-form-label">Asignar al Rol <span class="text-danger">*</span></label>
                                    <select class="form-select ui-input cursor-pointer" name="permisoRol" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        @foreach ($roles as $i => $rol)
                                            <option value="{{ $rol->id }}">{{ strtoupper($rol->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <button class="ui-btn-primary w-100" type="submit">
                                        <i class="bi bi-node-plus-fill"></i> Vincular
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end mb-3 mt-4 px-1">
            <div class="d-flex align-items-center gap-3">
                <div class="ui-icon-box ui-pastel-blue" style="width: 44px; height: 44px;">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1 fs-18">Estructura Organizacional</h5>
                    <p class="text-muted fs-14 mb-0">Listado de roles configurados y su matriz de accesos.</p>
                </div>
            </div>
            <span class="badge ui-pastel-blue rounded-pill px-3 py-2 fs-13 shadow-sm border border-light d-none d-sm-block">
                {{ count($roles) }} Roles Registrados
            </span>
        </div>

        <div class="accordion ui-accordion mb-5" id="accordionRoles">
            @foreach ($roles as $i => $rol)
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="heading-{{ $i }}">
                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center" 
                                type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapse-{{ $i }}" 
                                aria-expanded="false">
                            
                            <div class="d-flex align-items-center w-100 pe-3">
                                <i class="bi bi-shield-check text-indigo me-3 fs-4" style="color: #6366f1;"></i> 
                                <span class="letter-spacing-1">{{ strtoupper($rol->name) }}</span>
                                
                                <span class="ms-auto badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1 fw-medium fs-12">
                                    {{ count($rol->permissionsRole) }} permisos
                                </span>
                            </div>
                        </button>
                    </h2>
                    
                    <div id="collapse-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionRoles">
                        <div class="accordion-body p-4 p-md-5 bg-light">
                            
                            <form action="{{ route('admin.roles.update', $rol->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white p-4 rounded-4 border border-light-subtle shadow-sm mb-4">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Matriz de Autorización</h6>
                                        <p class="fs-13 text-muted mb-0">Enciende o apaga los módulos a los que este rol tiene acceso.</p>
                                    </div>
                                    <button type="submit" class="ui-btn-primary mt-3 mt-sm-0 shadow-sm">
                                        <i class="bi bi-save me-2"></i> Actualizar Permisos
                                    </button>
                                </div>

                                <div class="row g-4">
                                    @forelse ($rol->permissionsRole as $listapermisos)
                                        <div class="col-lg-6 col-xl-4">
                                            <div class="ui-card p-3 h-100 border-0">
                                                <div class="form-check form-switch ui-switch d-flex align-items-center justify-content-between w-100 m-0 p-0">
                                                    <label class="form-check-label user-select-none text-truncate pe-3 fw-bold fs-13" for="checkbox{{ $listapermisos->id }}">
                                                        {{ $listapermisos->name }}
                                                    </label>
                                                    <input type="checkbox" name="permissions[]" value="{{ $listapermisos->id }}"
                                                        class="form-check-input flex-shrink-0 m-0" id="checkbox{{ $listapermisos->id }}"
                                                        @if (in_array($listapermisos->id, $rol->permissions->pluck('id')->toArray())) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <div class="ui-icon-box bg-white text-muted mx-auto mb-3 shadow-sm" style="width: 64px; height: 64px; font-size: 2rem;">
                                                <i class="bi bi-inbox"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark">Rol sin permisos</h6>
                                            <p class="text-muted fs-14 mb-0">Utiliza el botón superior para crear y vincular reglas a este rol.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Validación de formularios Bootstrap
            $('#formAddRole, #formAddPermiso').submit(function(event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });

            // Lógica para deslizar formularios sin saltos bruscos
            $('#mostrarCardRole').on('click', function(e) {
                e.preventDefault();
                if($('#cardAddPermisos').is(':visible')) {
                    $('#cardAddPermisos').slideUp(250, function() {
                        $('#cardAddRole').slideDown(350, function() {
                            $('input[name="namerole"]').focus();
                        });
                    });
                } else {
                    $('#cardAddRole').slideToggle(350, function() {
                        if($(this).is(':visible')) $('input[name="namerole"]').focus();
                    });
                }
            });

            $('#mostrarCardPermisos').on('click', function(e) {
                e.preventDefault();
                if($('#cardAddRole').is(':visible')) {
                    $('#cardAddRole').slideUp(250, function() {
                        $('#cardAddPermisos').slideDown(350, function() {
                            $('input[name="permisoName"]').focus();
                        });
                    });
                } else {
                    $('#cardAddPermisos').slideToggle(350, function() {
                        if($(this).is(':visible')) $('input[name="permisoName"]').focus();
                    });
                }
            });

            // Cerrar formulario manualmente
            $('.btn-cerrar-form').on('click', function() {
                $(this).closest('.col-12').slideUp(300);
            });
        });
    </script>
</x-base-layout>