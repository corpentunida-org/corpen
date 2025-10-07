/* ============================================================
   🌸 INTERACCIONES MINIMALISTAS PASTELES
   Funciones: selección múltiple, filtros, acciones masivas, exportación.
   Autor: Tú 💫
============================================================ */

document.addEventListener("DOMContentLoaded", () => {
    const selected = new Set();
    let autoRefresh = null;
    let realTime = true;

    init();

    /* ========================
       🔧 INICIALIZACIÓN
    ======================== */
    function init() {
        initTooltips();
        bindEvents();
        loadPreferences();
        autoCloseAlerts();
    }

    function initTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }

    function autoCloseAlerts() {
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    }

    /* ========================
       🎛️ EVENTOS PRINCIPALES
    ======================== */
    function bindEvents() {
        on('#select-all', 'change', toggleSelectAll);
        on('#select-all-table', 'change', toggleSelectAll);
        onAll('.interaction-checkbox', 'change', toggleSelect);
        on('#refresh-btn', 'click', refreshData);
        on('#auto-refresh-btn', 'click', toggleAutoRefresh);
        onAll('.export-btn', 'click', exportData);
        onAll('.bulk-action-btn', 'click', bulkAction);
        onAll('.delete-interaction', 'click', confirmDelete);
        onAll('.quick-date-filter', 'click', quickDateFilter);
        on('#per-page', 'change', changePerPage);
        onAll('.sortable', 'click', sortTable);
        on('#save-filter-btn', 'click', showSaveFilterModal);
        on('#confirmSaveFilter', 'click', saveFilters);

        initKeyboardShortcuts();
        initSearch();
    }

    /* ========================
       ⌨️ ATAJOS DE TECLADO
    ======================== */
    function initKeyboardShortcuts() {
        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = newRoute("interactions.create");
            }
            if (e.ctrlKey && e.key === 'r') refreshData();
            if (e.ctrlKey && e.key === 'a' && !isInput(e.target)) {
                e.preventDefault();
                click('#select-all');
            }
            if (e.ctrlKey && e.key === 'v') cycleViewMode();
            if (e.key === 'Escape') clearSelections();
            if (e.key === 'F11') {
                e.preventDefault();
                toggleFullscreen();
            }
        });
    }

    /* ========================
       🧩 SELECCIÓN DE ÍTEMS
    ======================== */
    function toggleSelectAll(e) {
        const check = e.target.checked;
        document.querySelectorAll('.interaction-checkbox').forEach(cb => {
            cb.checked = check;
            updateSelection(cb.value, check);
        });
        updateUI();
    }

    function toggleSelect(e) {
        updateSelection(e.target.value, e.target.checked);
        updateUI();
    }

    function updateSelection(id, checked) {
        checked ? selected.add(id) : selected.delete(id);
    }

    function updateUI() {
        const count = selected.size;
        toggleDisplay('#selected-count', count > 0);
        toggleDisplay('#bulk-actions', count > 0);
        setText('#selected-number', count);

        document.querySelectorAll('.interaction-card').forEach(card => {
            const cb = card.querySelector('.interaction-checkbox');
            card.classList.toggle('selected', cb?.checked);
        });

        updateSelectAllState();
    }

    function updateSelectAllState() {
        const all = qsa('.interaction-checkbox');
        const checked = selected.size;
        const boxes = qsa('#select-all, #select-all-table');
        boxes.forEach(cb => {
            cb.checked = checked === all.length;
            cb.indeterminate = checked > 0 && checked < all.length;
        });
    }

    /* ========================
       📅 FILTROS RÁPIDOS
    ======================== */
    function quickDateFilter(e) {
        const period = e.target.dataset.period;
        const from = qs('#from');
        const to = qs('#to');
        const today = new Date();

        qsa('.quick-date-filter').forEach(btn => btn.classList.remove('active'));
        e.target.classList.add('active');

        const ranges = {
            today: [today, today],
            yesterday: [addDays(today, -1), addDays(today, -1)],
            week: [addDays(today, -today.getDay()), today],
            month: [new Date(today.getFullYear(), today.getMonth(), 1), today],
            quarter: [new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1), today],
            year: [new Date(today.getFullYear(), 0, 1), today],
        };

        const [start, end] = ranges[period] || [today, today];
        from.value = formatDate(start);
        to.value = formatDate(end);

        if (realTime) qs('#filters-form').submit();
    }

    /* ========================
       🔁 ACTUALIZAR Y AUTOREFRESCO
    ======================== */
    function refreshData() {
        showLoading();
        setTimeout(() => window.location.reload(), 500);
    }

    function toggleAutoRefresh() {
        const btn = qs('#auto-refresh-btn');
        if (autoRefresh) {
            clearInterval(autoRefresh);
            autoRefresh = null;
            btn.classList.remove('active');
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Auto-actualizar';
        } else {
            autoRefresh = setInterval(refreshData, 30000);
            btn.classList.add('active');
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Detener auto-actualizar';
        }
    }

    /* ========================
       📤 EXPORTAR DATOS
    ======================== */
    function exportData(e) {
        const format = e.target.dataset.format;
        if (!selected.size) return warn('Selecciona al menos una interacción.');

        showLoading('Preparando exportación...');
        setTimeout(() => {
            hideLoading();
            notify(`Los datos se exportarán en formato ${format.toUpperCase()}.`);
        }, 2000);
    }

    /* ========================
       🗂️ ACCIONES MASIVAS
    ======================== */
    function bulkAction(e) {
        const action = e.target.dataset.action;
        if (!selected.size) return warn('Debes seleccionar al menos una interacción.');
        showBulkModal(action);
    }

    function showBulkModal(action) {
        const modal = new bootstrap.Modal(qs('#bulkActionModal'));
        const body = qs('#bulkActionModalBody');
        const title = qs('#bulkActionModalLabel');

        const count = selected.size;
        const templates = {
            delete: `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ¿Eliminar ${count} interacciones? No se puede deshacer.
                </div>`,
            assign: `
                <div class="mb-3">
                    <label class="form-label">Asignar agente</label>
                    <select class="form-select" id="bulkAgent">
                        <option>Seleccionar agente...</option>
                    </select>
                </div>`,
            status: `
                <div class="mb-3">
                    <label class="form-label">Nuevo estado</label>
                    <select class="form-select" id="bulkStatus">
                        <option>✅ Exitoso</option>
                        <option>⏳ Pendiente</option>
                        <option>❌ Fallido</option>
                        <option>🔄 Seguimiento</option>
                    </select>
                </div>`
        };

        title.textContent = action === "delete" ? "Eliminar interacciones" :
                            action === "assign" ? "Asignar agente" : "Cambiar estado";

        body.innerHTML = templates[action] || '';
        on('#confirmBulkAction', 'click', () => execBulk(action, count));
        modal.show();
    }

    function execBulk(action, count) {
        showLoading('Procesando...');
        setTimeout(() => {
            hideLoading();
            clearSelections();
            notify(`La acción ${action} se aplicó a ${count} interacciones.`);
            setTimeout(() => window.location.reload(), 1000);
        }, 2000);
    }

    /* ========================
       🧹 UTILIDADES
    ======================== */
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function addDays(d, n) {
        const nd = new Date(d);
        nd.setDate(nd.getDate() + n);
        return nd;
    }

    function showLoading(msg = 'Cargando...') {
        qs('#loading-overlay p').textContent = msg;
        qs('#loading-overlay').classList.remove('d-none');
    }

    function hideLoading() {
        qs('#loading-overlay').classList.add('d-none');
    }

    function clearSelections() {
        selected.clear();
        qsa('.interaction-checkbox').forEach(cb => cb.checked = false);
        updateUI();
    }

    function notify(msg) {
        Swal.fire({ icon: 'success', title: 'Éxito', text: msg, timer: 3000, showConfirmButton: false });
    }

    function warn(msg) {
        Swal.fire({ icon: 'warning', title: 'Aviso', text: msg });
    }

    function toggleFullscreen() {
        document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen();
    }

    /* ========================
       🪄 HELPERS RÁPIDOS
    ======================== */
    const qs = s => document.querySelector(s);
    const qsa = s => document.querySelectorAll(s);
    const on = (s, e, f) => qs(s)?.addEventListener(e, f);
    const onAll = (s, e, f) => qsa(s).forEach(el => el.addEventListener(e, f));
    const click = s => qs(s)?.click();
    const isInput = el => ['input', 'textarea', 'select'].includes(el.tagName.toLowerCase());
    const toggleDisplay = (s, show) => qs(s).style.display = show ? 'block' : 'none';
    const setText = (s, txt) => qs(s).textContent = txt;
});
