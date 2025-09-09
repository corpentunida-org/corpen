<x-base-layout>
    <div class="container">
        <div class="top-section">
            <form method="GET" action="{{ route('interactions.index') }}" class="search-form">
                <div class="search-group">
                    <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="Buscar en todo...">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('q'))
                        <a href="{{ route('interactions.index') }}" class="btn-clear" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
            <a href="{{ route('interactions.create') }}" class="btn-create btn-create-action">
                <i class="fas fa-plus"></i> Registrar Nueva Interacción
            </a>
        </div>

        <h1 class="title">Gestión de Interacciones con Asociados</h1>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($interactions->isEmpty())
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>No se encontraron interacciones registradas. Comience a añadir nuevas interacciones.</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Agente</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Resultado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($interactions as $interaction)
                            <tr>
                                <td>{{ $interaction->id }}</td>
                                
                                <td>
                                    @if($interaction->client)
                                        <span class="client-info">
                                            <strong>{{ $interaction->client->cod_ter }}</strong> - 
                                            {{ $interaction->client->apl1 }} {{ $interaction->client->apl2 }} 
                                            {{ $interaction->client->nom1 }} {{ $interaction->client->nom2 }}
                                        </span>
                                    @else
                                        <span class="text-placeholder">N/A</span>
                                    @endif
                                </td>

                                <td><span class="agent-name">{{ $interaction->agent->name ?? 'Sin asignar' }}</span></td>

                                <td>{{ $interaction->interaction_date->format('d/m/Y H:i') }}</td>

                                <td>
                                    <span class="status-tag type-{{ strtolower(str_replace(' ', '-', $interaction->interaction_type)) }}">
                                        {{ $interaction->interaction_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-tag outcome-{{ strtolower(str_replace(' ', '-', $interaction->outcome)) }}">
                                        {{ $interaction->outcome }}
                                    </span>
                                </td>

                                <td class="actions">
                                    <!-- Botón Ver -->
                                    <a href="{{ route('interactions.show', $interaction) }}" class="btn-action btn-view btn-view-action" title="Ver Interacción">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Botón Editar -->
                                    <a href="{{ route('interactions.edit', $interaction) }}" class="btn-action btn-edit btn-edit-action" title="Editar Interacción">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('interactions.destroy', $interaction) }}" method="POST" class="delete-form" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete btn-delete-action" title="Eliminar Interacción">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-links">
                {{ $interactions->links() }}
            </div>
        @endif
    </div>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sweetColor = '#8A2BE2';

            // Crear
            document.querySelector('.btn-create-action').addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: 'Nueva Interacción',
                    text: 'Vas a registrar una nueva interacción.',
                    icon: 'info',
                    confirmButtonColor: sweetColor,
                    confirmButtonText: 'Continuar',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // Editar
            document.querySelectorAll('.btn-edit-action').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    Swal.fire({
                        title: 'Editar Interacción',
                        text: 'Vas a editar esta interacción.',
                        icon: 'warning',
                        confirmButtonColor: sweetColor,
                        confirmButtonText: 'Editar',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });

            // Eliminar
            document.querySelectorAll('.btn-delete-action').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Esta interacción se eliminará de forma permanente.',
                        icon: 'error',
                        confirmButtonColor: sweetColor,
                        confirmButtonText: 'Sí, eliminar',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Ver Interacción
            document.querySelectorAll('.btn-view-action').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    Swal.fire({
                        title: 'Ver Interacción',
                        text: 'Vas a visualizar esta interacción.',
                        icon: 'info',
                        confirmButtonColor: sweetColor,
                        confirmButtonText: 'Continuar',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        });
    </script>

    {{-- Estilos completos --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@500;700&display=swap');
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

        :root {
            --primary-corporate: #0056b3;
            --secondary-corporate: #007bff;
            --text-dark: #2c3e50;
            --text-medium: #555;
            --text-light: #888;
            --bg-page: #f8f9fa;
            --bg-card: #ffffff;
            --border-subtle: #dee2e6;
            --shadow-corporate: rgba(0, 0, 0, 0.08);
            --shadow-hover: rgba(0, 0, 0, 0.12);
        }

        body { font-family: 'Roboto', sans-serif; margin: 0; background-color: var(--bg-page); color: var(--text-medium); line-height: 1.6; }
        .container { 
            max-width: 95%; /* Ajustado para ser más ancho y ocupar el espacio marcado */
            margin: 50px auto; 
            background-color: var(--bg-card); 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px var(--shadow-corporate); 
            transition: all 0.3s ease; 
        }
        .container:hover { box-shadow: 0 6px 20px var(--shadow-hover); }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap; /* Permite que los elementos se envuelvan en pantallas pequeñas */
            gap: 20px; /* Espacio entre los elementos */
        }
        
        .title { 
            font-family: 'Montserrat', sans-serif; 
            color: var(--text-dark); 
            text-align: center; 
            margin-bottom: 50px; 
            font-size: 2.2rem; 
            font-weight: 700; 
            letter-spacing: 0.5px; 
            position: relative; 
            text-transform: uppercase; 
        }
        .title::after { content: ''; position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); width: 70px; height: 3px; background-color: var(--primary-corporate); border-radius: 2px; opacity: 0.8; }

        .alert { padding: 15px 20px; margin-bottom: 25px; border-radius: 6px; display: flex; align-items: center; font-weight: 500; font-size: 0.95rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid; }
        .alert i { margin-right: 12px; font-size: 1.3rem; }
        .alert-success { background-color: #e9f7ef; color: #28a745; border-color: #c3e6cb; }
        .alert-error { background-color: #fcebeb; color: #dc3545; border-color: #f5c6cb; }

        /* Eliminamos header-actions ya que ahora se integra en top-section */
        /* .header-actions { display: flex; justify-content: flex-end; margin-bottom: 30px; } */ 
        
        .btn-create { background-color: var(--primary-corporate); color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; font-weight: 500; font-size: 0.95rem; letter-spacing: 0.5px; transition: background-color 0.3s ease, transform 0.2s ease; box-shadow: 0 3px 10px rgba(0, 86, 179, 0.3); }
        .btn-create i { margin-right: 10px; font-size: 0.9rem; }
        .btn-create:hover { background-color: #004494; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0, 86, 179, 0.4); }

        .empty-state { text-align: center; padding: 60px 20px; background-color: #f2f4f6; border: 1px dashed var(--border-subtle); border-radius: 8px; margin-top: 30px; color: var(--text-light); }
        .empty-state i { font-size: 4rem; color: #c0c8d1; margin-bottom: 20px; }
        .empty-state p { font-size: 1.05rem; font-weight: 400; }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; background-color: var(--bg-card); border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px var(--shadow-corporate); }
        th, td { 
            padding: 15px 20px; 
            text-align: left; 
            border-bottom: 1px solid var(--border-subtle); 
            white-space: nowrap; /* Evita que el texto se envuelva */
            overflow: hidden; /* Oculta el contenido que excede el ancho */
            text-overflow: ellipsis; /* Añade puntos suspensivos si el texto es muy largo */
            max-width: 200px; /* Limita el ancho de la celda si es necesario, ajusta este valor */
        }
        th { background-color: #e9ecef; color: var(--text-dark); font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.7px; }
        tr:last-child td { border-bottom: none; }
        tbody tr:hover { background-color: #f5f5f5; }

        .client-info { 
            white-space: nowrap; /* Asegura que la información del cliente no se envuelva */
            overflow: hidden;
            text-overflow: ellipsis;
            display: block; /* Necesario para que overflow y text-overflow funcionen */
        }
        .client-info strong { color: var(--text-dark); }
        .agent-name { font-weight: 500; color: var(--text-dark); white-space: nowrap; }
        .text-placeholder { color: var(--text-light); font-style: italic; white-space: nowrap; }

        .status-tag { 
            display: inline-block; 
            padding: 6px 10px; 
            border-radius: 4px; 
            font-size: 0.85rem; 
            font-weight: 500; 
            text-transform: uppercase; 
            white-space: nowrap; /* Asegura que la etiqueta de estado no se envuelva */
            letter-spacing: 0.3px; 
        }

        /* Tipos de interacción */
        .status-tag.type-llamada { background-color: #28a745; color: #fff; }
        .status-tag.type-correo { background-color: #17a2b8; color: #fff; }
        .status-tag.type-reunion { background-color: #ffc107; color: #333; }
        .status-tag.type-whatsapp { background-color: #20c997; color: #fff; }

        /* Resultados */
        .status-tag.outcome-exitoso { background-color: #28a745; color: #fff; }
        .status-tag.outcome-pendiente { background-color: #ffc107; color: #333; }
        .status-tag.outcome-fallido { background-color: #dc3545; color: #fff; }
        .status-tag.outcome-seguimiento { background-color: #6c757d; color: #fff; }

        .actions { white-space: nowrap; display: flex; gap: 8px; }
        .btn-action { padding: 8px 12px; border-radius: 4px; color: white; border: none; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.2s ease, transform 0.2s ease; box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
        .btn-action i { font-size: 0.85rem; }
        .btn-action:hover { opacity: 0.95; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .btn-view { background-color: #17a2b8; }
        .btn-edit { background-color: var(--secondary-corporate); }
        .btn-delete { background-color: #dc3545; }
        .btn-view:hover { background-color: #138496; }
        .btn-edit:hover { background-color: #0069d9; }
        .btn-delete:hover { background-color: #c82333; }

        .pagination-links { margin-top: 35px; display: flex; justify-content: center; font-family: 'Roboto', sans-serif; }
        .pagination-links nav { display: flex; gap: 6px; }
        .pagination-links nav a, .pagination-links nav span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 8px; border-radius: 4px; text-decoration: none; color: var(--text-medium); background-color: var(--bg-card); border: 1px solid var(--border-subtle); transition: all 0.2s ease; font-weight: 400; }
        .pagination-links nav a:hover { background-color: #e9ecef; border-color: var(--secondary-corporate); color: var(--primary-corporate); box-shadow: 0 1px 5px rgba(0,0,0,0.08); transform: translateY(-1px); }

        .search-form { margin-bottom: 0; display: flex; justify-content: flex-start; } /* Ajustado para el nuevo layout */
        .search-group { display: flex; gap: 8px; align-items: center; }
        .search-input { padding: 10px 12px; border: 1px solid var(--border-subtle); border-radius: 4px; font-size: 0.95rem; outline: none; width: 280px; }
        .btn-search { background-color: var(--secondary-corporate); color: white; padding: 10px 14px; border: none; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.3s ease; }
        .btn-search:hover { background-color: #0069d9; }
        .btn-clear { background-color: #e0e0e0; color: #555; padding: 10px 14px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn-clear:hover { background-color: #d6d6d6; }

        /* Media queries para responsividad si es necesario */
        @media (max-width: 768px) {
            .container {
                max-width: 98%;
                margin: 20px auto;
                padding: 20px;
            }
            .top-section {
                flex-direction: column;
                align-items: flex-start;
            }
            .search-form {
                width: 100%;
                margin-bottom: 20px;
            }
            .search-input {
                width: calc(100% - 100px); /* Ajusta el ancho para los botones de búsqueda y limpiar */
            }
            .btn-create {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</x-base-layout>