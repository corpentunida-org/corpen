<x-base-layout>
    @section('titlepage', 'Administrador')
    <x-success />
    <x-error />
    <div class="col-xxl-12 col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Usuarios</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand">
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="" data-bs-original-title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('admin.users.create') }}" class="dropdown-item"><i
                                    class="feather-plus"></i>Crear Usuario</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                <form action="{{ route('admin.users.show', 1) }}" method="GET">
                    <div class="mb-4 pb-1 d-flex">
                        <input type="text" name="query" id="userSearch" class="form-control"
                            placeholder="Buscar por nombre de usuario...">
                        <button type="submit" class="btn btn-primary ms-2">Buscar</button>
                    </div>
                </form>
                <div id="userList">
                    @if (isset($usuariosfiltrados))
                        <div id="userList">
                            @forelse ($usuariosfiltrados as $user)
                                <div class="user-item w-100 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-image me-3">
                                            <i class="bi bi-person-circle fs-3"></i>
                                        </div>
                                        <div>
                                            <a href="#"
                                                class="d-flex align-items-center mb-1 user-name">{{ $user->name }}</a>
                                            <div class="fs-12 fw-normal text-muted user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    <div class="dropdown hstack text-end justify-content-end">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="feather feather-more-vertical"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="dropdown-item">
                                                    <i class="feather feather-eye me-3"></i>
                                                    <span>Ver detalle</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item">
                                                    <i class="feather feather-share-2 me-3"></i>
                                                    <span>Compartir</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <hr class="border-dashed my-3">
                            @empty
                                <p>No se encontraron usuarios.</p>
                            @endforelse
                        </div>
                    @endif
                    @if (isset($users))
                        @foreach ($users as $user)
                            <div class="user-item w-100 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-image me-3">
                                        <i class="bi bi-person-circle fs-3"></i>
                                    </div>
                                    <div>
                                        <a href="#"
                                            class="d-flex align-items-center mb-1 user-name">{{ $user->name }}</a>
                                        <div class="fs-12 fw-normal text-muted user-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="dropdown hstack text-end justify-content-end">
                                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="feather feather-more-vertical"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                                <i class="feather feather-eye me-3"></i>
                                                <span>Ver detalle</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item">
                                                <i class="feather feather-share-2 me-3"></i>
                                                <span>Compartir</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <hr class="border-dashed my-3">
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card-footer d-flex justify-content-center">
                @if (isset($users))
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
    {{-- ----------------------------------------------------------------------------------- --}}
</x-base-layout>
