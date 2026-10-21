{{-- resources/views/rsv/admin/partials/calendario/bloqueos.blade.php --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0" style="color: #475569; font-weight: 600;">
            <i class="bi bi-tools me-2" style="color: #c98a85;"></i>Gestión de Bloqueos / Mantenimiento
        </h6>
        <button class="btn btn-sm" style="background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; font-weight: 600;" onclick="abrirModalBloqueo()">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Bloqueo
        </button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap" id="tablaBloqueos" style="font-size: 13px;">
                <thead style="background: #fafbfd; color: #64748b;">
                    <tr>
                        <th class="fw-semibold border-bottom-0">Inmueble</th>
                        <th class="fw-semibold border-bottom-0">Desde</th>
                        <th class="fw-semibold border-bottom-0">Hasta</th>
                        <th class="fw-semibold border-bottom-0">Motivo</th>
                        <th class="fw-semibold border-bottom-0 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-bloqueos-body">
                    <!-- Llenado vía JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Bloqueo -->
<div class="modal fade" id="modalBloqueo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: #fafbfd; border-bottom: 1px solid #f1f5f9;">
                <h6 class="modal-title" id="modalBloqueoTitle" style="color: #475569; font-weight: 600;">Nuevo Bloqueo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBloqueo" onsubmit="guardarBloqueo(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="bloqueo_id">

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #64748b;">Inmueble</label>
                        <select class="form-select form-select-sm" id="id_rsv_catalogo_inmueble" required>
                            <option value="">Seleccione un inmueble...</option>
                            @foreach(($listaInmuebles ?? []) as $inm)
                                <option value="{{ $inm->id }}">{{ $inm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #64748b;">Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm" id="fecha_inicio" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; font-weight: 600; color: #64748b;">Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm" id="fecha_fin" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; font-weight: 600; color: #64748b;">Motivo / Detalle</label>
                        <textarea class="form-control form-control-sm" id="motivo" rows="3" required placeholder="Ej: Mantenimiento de tuberías..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm" style="background: #c98a85; color: white; border: none;" id="btnSaveBloqueo">Guardar Bloqueo</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const urlBaseBloqueos = '/rsv/bloqueos-calendario';
    let modalBloqueoInst;

    document.addEventListener('DOMContentLoaded', () => {
        modalBloqueoInst = new bootstrap.Modal(document.getElementById('modalBloqueo'));
        cargarBloqueos();
    });

    function cargarBloqueos() {
        fetch(urlBaseBloqueos, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('lista-bloqueos-body');
                tbody.innerHTML = '';
                const registros = data.data?.data || data.data || [];

                if(registros.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">No hay bloqueos registrados.</td></tr>`;
                    return;
                }

                registros.forEach(b => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><span style="font-weight: 500; color: #475569;">${b.inmueble?.name || 'N/A'}</span></td>
                        <td>${b.fecha_inicio.split('T')[0]}</td>
                        <td>${b.fecha_fin.split('T')[0]}</td>
                        <td><span class="text-truncate d-inline-block" style="max-width: 200px;">${b.motivo}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light py-0 px-2 me-1" onclick='editarBloqueo(${JSON.stringify(b)})' title="Editar"><i class="bi bi-pencil" style="font-size: 11px;"></i></button>
                            <button class="btn btn-sm btn-light py-0 px-2 text-danger" onclick='eliminarBloqueo(${b.id})' title="Eliminar"><i class="bi bi-trash" style="font-size: 11px;"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }).catch(e => console.error("Error cargando bloqueos:", e));
    }

    function abrirModalBloqueo() {
        document.getElementById('formBloqueo').reset();
        document.getElementById('bloqueo_id').value = '';
        document.getElementById('modalBloqueoTitle').innerText = 'Nuevo Bloqueo';
        modalBloqueoInst.show();
    }

    function editarBloqueo(b) {
        document.getElementById('bloqueo_id').value = b.id;
        document.getElementById('id_rsv_catalogo_inmueble').value = b.id_rsv_catalogo_inmueble;
        document.getElementById('fecha_inicio').value = b.fecha_inicio.split('T')[0];
        document.getElementById('fecha_fin').value = b.fecha_fin.split('T')[0];
        document.getElementById('motivo').value = b.motivo;

        document.getElementById('modalBloqueoTitle').innerText = 'Editar Bloqueo';
        modalBloqueoInst.show();
    }

    function guardarBloqueo(e) {
        e.preventDefault();
        const id = document.getElementById('bloqueo_id').value;
        const payload = {
            id_rsv_catalogo_inmueble: document.getElementById('id_rsv_catalogo_inmueble').value,
            fecha_inicio: document.getElementById('fecha_inicio').value,
            fecha_fin: document.getElementById('fecha_fin').value,
            motivo: document.getElementById('motivo').value,
            _token: '{{ csrf_token() }}'
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `${urlBaseBloqueos}/${id}` : urlBaseBloqueos;

        document.getElementById('btnSaveBloqueo').disabled = true;

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error en la petición');
            return data;
        })
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Éxito', text: data.message, timer: 1500, showConfirmButton: false });
            modalBloqueoInst.hide();
            cargarBloqueos();
            if(typeof calendar !== 'undefined') calendar.refetchEvents(); // Recargar calendario global si existe
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Atención', text: err.message });
        })
        .finally(() => {
            document.getElementById('btnSaveBloqueo').disabled = false;
        });
    }

    function eliminarBloqueo(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "El registro desaparecerá de las vistas (Eliminación Lógica).",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c98a85',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${urlBaseBloqueos}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire('Eliminado', data.message, 'success');
                        cargarBloqueos();
                        if(typeof calendar !== 'undefined') calendar.refetchEvents();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
