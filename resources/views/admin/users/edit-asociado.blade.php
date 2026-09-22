<x-base-layout>
    @section('titlepage', 'Perfil de Asociado')

    <x-success />
    <x-error />

    <style>
        .ui-card {
            background: #ffffff;
            border: 1px solid #eaedf1;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04);
            overflow: hidden;
        }
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
            width: 100%;
        }
        .ui-input:focus {
            background: #ffffff;
            border-color: #8ec5fc;
            box-shadow: 0 0 0 4px rgba(142, 197, 252, 0.2);
            outline: none;
        }
        .ui-switch .form-check-input {
            height: 1.5rem; width: 2.75rem; border-radius: 2rem; cursor: pointer;
            border-color: #cbd5e1; background-color: #e2e8f0;
        }
        .ui-switch .form-check-input:checked { background-color: #6366f1; border-color: #6366f1; }
        .ui-danger-zone {
            border: 1px solid #fecaca; background: #fff5f5;
            border-radius: 12px; padding: 1.5rem;
        }
        .ui-btn-danger {
            background: #ef4444; color: #fff; border: none; border-radius: 8px;
            padding: 0.75rem 1.5rem; font-weight: 600;
        }
        .ui-btn-primary {
            background: #4f46e5; color: #fff; border: none; border-radius: 8px;
            padding: 0.75rem 2rem; font-weight: 600;
        }
        .ui-back-link {
            display: inline-flex; align-items: center; font-weight: 600; color: #64748b;
            text-decoration: none; margin-bottom: 1rem;
        }
        .ui-back-link:hover { color: #4f46e5; }

        /* Estilos que requiere partials/matriz-roles-permisos (compartida con edit.blade.php) */
        .ui-icon-box {
            width: 44px; height: 44px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;
        }
        .ui-pastel-blue { background: #e0f2fe; color: #0284c7; }
        .ui-pastel-purple { background: #f3e8ff; color: #9333ea; }
        .ui-pastel-amber { background: #fef3c7; color: #d97706; }
        .ui-accordion .accordion-item {
            border: 1px solid #e2e8f0; border-radius: 12px !important;
            margin-bottom: 0.85rem; overflow: hidden; background: #ffffff;
        }
        .ui-accordion .accordion-button {
            background: #ffffff; font-weight: 600; color: #1e293b;
            padding: 1.15rem 1.5rem; box-shadow: none !important;
        }
        .ui-accordion .accordion-button:not(.collapsed) {
            background: #f8fafc; color: #6366f1; border-bottom: 1px solid #e2e8f0;
        }
        .ui-accordion .accordion-button::after { filter: contrast(0.5); }
    </style>

    <div class="container-fluid px-0">
        <a href="{{ route('admin.users.index', ['tipo' => 'asociados']) }}" class="ui-back-link">
            <i class="bi bi-people fs-6 me-2"></i> Volver al listado de asociados
        </a>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card p-4 p-md-5 mb-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-person-vcard me-2 text-primary"></i>Datos del Asociado</h5>
                    <p class="text-muted fs-13 mb-4">Este perfil se autoregistró desde el portal de Reservas — su cédula y fecha de nacimiento ya fueron verificadas contra SiaSoft y no se editan aquí.</p>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-form-label">Cédula</label>
                                <input type="text" class="ui-input" value="{{ $user->nid }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Fecha de Nacimiento</label>
                                <input type="text" class="ui-input" value="{{ $user->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="ui-input" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="ui-input" name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Teléfono</label>
                                <input type="text" class="ui-input" name="telefono" value="{{ old('telefono', $user->telefono) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Fecha de Registro</label>
                                <input type="text" class="ui-input" value="{{ $user->created_at?->format('d/m/Y H:i') ?? 'No disponible' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-form-label">Asignar Contraseña</label>
                                <input type="password" class="ui-input" name="pass" placeholder="Dejar en blanco para mantener actual">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check ui-switch">
                                    <input type="checkbox" class="form-check-input" id="forzar_cambio_password" name="forzar_cambio_password" value="1">
                                    <label class="form-check-label ms-2" for="forzar_cambio_password">
                                        Forzar cambio de contraseña en el próximo inicio de sesión
                                    </label>
                                </div>
                            </div>
                        </div>

                        @include('admin.users.partials.matriz-roles-permisos')
                    </form>
                </div>

                @include('admin.users.partials.copiar-perfil')

                @include('admin.users.partials.revocar-perfil')
            </div>

            <div class="col-lg-4">
                <div class="ui-card p-4 mb-4 text-center">
                    @php
                        $nombres = explode(' ', trim($user->name));
                        $iniciales = strtoupper(substr($nombres[0] ?? 'U', 0, 1) . substr($nombres[1] ?? '', 0, 1));
                    @endphp
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary mx-auto mb-3"
                        style="width: 90px; height: 90px; background-color: #e0f2fe; font-size: 2rem;">
                        {{ $iniciales }}
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $user->name }}</h6>
                    <p class="text-muted fs-13 mb-2">{{ $user->email }}</p>
                    @if ($user->bloqueado)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Bloqueado</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                    @endif

                    <form method="POST" action="{{ route('admin.impersonar.iniciar', $user->id) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary w-100 fw-semibold">
                            <i class="bi bi-eye-fill me-1"></i> Ver como este asociado
                        </button>
                    </form>
                    <p class="text-muted fs-12 mt-2 mb-0">
                        Para reproducir exactamente lo que él ve, sin conocer ni cambiar su contraseña — útil para soportes.
                    </p>
                </div>

                <div class="ui-danger-zone">
                    <h6 class="fw-bold text-danger mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $user->bloqueado ? 'Cuenta Bloqueada' : 'Bloquear o Eliminar' }}
                    </h6>
                    <p class="text-danger opacity-75 fs-13 mb-3">
                        Bloquear impide el inicio de sesión sin borrar nada. Eliminar quita al asociado del listado.
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <form method="POST" action="{{ route($user->bloqueado ? 'admin.users.desbloquear' : 'admin.users.bloquear', $user->id) }}">
                            @csrf
                            <button type="submit" class="btn {{ $user->bloqueado ? 'btn-outline-success' : 'btn-outline-danger' }} w-100 fw-semibold">
                                <i class="bi bi-{{ $user->bloqueado ? 'unlock' : 'lock' }} me-1"></i>
                                {{ $user->bloqueado ? 'Desbloquear' : 'Bloquear' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                            onsubmit="return confirm('¿Eliminar a {{ addslashes($user->name) }}? Podrá restaurarse solo desde la base de datos.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ui-btn-danger w-100 fw-semibold">
                                <i class="bi bi-trash3-fill me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
