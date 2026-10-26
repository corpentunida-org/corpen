{{-- partials/reservas/origen_reserva.blade.php · CRUD Origen de Reserva --}}

<div class="reservas-soft p-3 bg-white rounded-3 border" id="orRoot" style="border-color: #f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
    .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
    .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
    .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; }
    .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
    .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
    .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
    .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
    .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; outline: 0; min-width: 200px; }
    .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; }
    .reservas-soft .count-badge b { color: var(--accent); }
</style>

    @php
        // SOLUCIÓN AL ERROR: Si la variable no viene del controlador principal, la consulta aquí mismo.
        $origenes = $origenes ?? \App\Models\Rsv\OrigenReserva::orderBy('nombre', 'asc')->get();

        // Resolutor de rutas dinámico
        $orRuta = function (array $cands, $param = null) {
            foreach ($cands as $c) {
                if (\Illuminate\Support\Facades\Route::has($c)) return $param !== null ? route($c, $param) : route($c);
            }
            return null;
        };
        $orStore   = $orRuta(['rsv.origenes.store', 'rsv.origen-reservas.store']);
        $orUpdate  = $orRuta(['rsv.origenes.update', 'rsv.origen-reservas.update'], 'ID');
        $orDestroy = $orRuta(['rsv.origenes.destroy', 'rsv.origen-reservas.destroy'], 'ID');
        $orEditable = !!($orStore || $orUpdate);
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
        <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
            <i class="bi bi-box-arrow-in-right me-1" style="font-size: 8px; color: #5d8a70;"></i> Orígenes de Reserva
        </h6>
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="count-badge"><b>{{ count($origenes ?? []) }}</b> orígenes</span>
            <input type="text" id="orSearch" class="xls-search" placeholder="Buscar origen…" autocomplete="off" style="width: 170px;">
            @if($orStore)
                <button type="button" class="btn-green" onclick="openOrModal()">
                    <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nuevo Origen
                </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
            <thead>
                <tr><th>Origen</th><th class="text-center" style="width: 90px;">·</th></tr>
            </thead>
            <tbody id="orTbody">
                @forelse($origenes as $o)
                    <tr class="or-row">
                        <td class="strong or-name-col">{{ $o->nombre }}</td>
                        <td class="text-center">
                            @if($orEditable)
                                <button type="button" class="btn-mini" onclick="openOrModal({{ $o->id }}, '{{ addslashes($o->nombre) }}')">Editar</button>
                            @endif
                            @if($orDestroy)
                                <button type="button" class="btn-mini" style="color:#c98a85;" onclick="confirmOrDelete({{ $o->id }}, '{{ addslashes($o->nombre) }}')">Del</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">Sin orígenes definidos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- MODAL FORM --}}
<div class="modal fade" id="orModalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" id="orModalTitle" style="font-size: 0.95rem;">Nuevo Origen</h6>
            </div>
            <form id="orForm">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="orId" name="id" value="">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8;">Nombre <span style="color:#c98a85;">*</span></label>
                        {{-- CAMBIO CRÍTICO: el name debe ser "nombre" para coincidir con la DB y el modelo --}}
                        <input type="text" class="form-control form-control-sm" id="orName" name="nombre" maxlength="255" required placeholder="Booking, Airbnb...">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL ELIMINAR --}}
<div class="modal fade" id="orModalDel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px;">
            <div class="modal-body p-3 text-center">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar origen</h6>
                <p class="mb-0" style="font-size: 8.5px; color: #94a3b8;">
                    Se eliminará <b id="orDelName" style="color: #334155;">—</b>
                </p>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #f1f5f9; padding: 9px 14px;">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="orDelGo" class="btn btn-sm py-1 px-2" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;"><i class="bi bi-trash3"></i> Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales para las rutas (usando route() de Laravel de forma segura)
    const OR_STORE_URL   = '{{ $orStore ?? "" }}';
    const OR_UPDATE_URL  = '{{ $orUpdate ?? "" }}';
    const OR_DESTROY_URL = '{{ $orDestroy ?? "" }}';
    // Se añade el prefijo "rsv." que exige tu grupo de rutas
    const OR_FRAG_URL    = '{{ route("rsv.admin.reservas.origen.fragmento") }}';

    // Función para recargar SOLO el fragmento HTML
    function reloadOrFragment() {
        fetch(OR_FRAG_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newContent = temp.querySelector('#orRoot');
                if (newContent) {
                    document.getElementById('orRoot').replaceWith(newContent);
                    const scripts = newContent.getElementsByTagName('script');
                    for (let s of scripts) eval(s.innerHTML);
                }
            });
    }

    // Buscador en tabla
    document.getElementById('orSearch')?.addEventListener('input', function() {
        const text = this.value.toLowerCase();
        document.querySelectorAll('#orTbody .or-row').forEach(row => {
            const name = row.querySelector('.or-name-col').textContent.toLowerCase();
            row.style.display = name.includes(text) ? '' : 'none';
        });
    });

    // Abrir Modal de Formulario
    window.openOrModal = function(id = null, name = '') {
        document.getElementById('orModalTitle').textContent = id ? 'Editar Origen' : 'Nuevo Origen';
        document.getElementById('orId').value = id || '';
        document.getElementById('orName').value = name;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalForm')).show();
    };

    // Confirmar Eliminación
    window.confirmOrDelete = function(id, name) {
        document.getElementById('orDelName').textContent = name;
        document.getElementById('orDelGo').onclick = function() {
            if(!OR_DESTROY_URL) return;
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('_method', 'DELETE');

            fetch(OR_DESTROY_URL.replace('ID', id), {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('orModalDel')).hide();
                reloadOrFragment();
            });
        };
        bootstrap.Modal.getOrCreateInstance(document.getElementById('orModalDel')).show();
    };

    // Envío del Formulario (Crear / Editar)
    document.getElementById('orForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('orId').value;
        const fd = new FormData(this);
        
        let url = OR_STORE_URL;
        if (id && OR_UPDATE_URL) {
            url = OR_UPDATE_URL.replace('ID', id);
            fd.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST', body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
        .then(res => {
            if (!res.ok) {
                let errorMsg = 'Error al guardar.';
                if (res.body.errors) {
                    errorMsg = Object.values(res.body.errors).map(err => err.join('\n')).join('\n');
                }
                alert(errorMsg);
                return;
            }
            
            bootstrap.Modal.getInstance(document.getElementById('orModalForm')).hide();
            reloadOrFragment();
        })
        .catch(() => alert('Error de conexión con el servidor.'));
    });
</script>