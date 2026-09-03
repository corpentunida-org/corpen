{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // 2. TOGGLE TABLA COLAPSABLE
    const toggleBtn    = document.getElementById('tableToggleBtn');
    const tableCollapse = document.getElementById('tableCollapse');
    const STORAGE_KEY  = 'ingesta_table_open_{{ $bloqueActivo }}';

    const hasFilters   = {{ (request('buscar_cedula') || request('estado')) ? 'true' : 'false' }};
    const wasOpen      = localStorage.getItem(STORAGE_KEY) === '1';

    function openTable() {
        tableCollapse.classList.add('is-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
        localStorage.setItem(STORAGE_KEY, '1');
    }
    function closeTable() {
        tableCollapse.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        localStorage.setItem(STORAGE_KEY, '0');
    }

    if (hasFilters || wasOpen) openTable();

    toggleBtn.addEventListener('click', function () {
        const isOpen = tableCollapse.classList.contains('is-open');
        isOpen ? closeTable() : openTable();
    });

    // 3. CHART.JS — GRÁFICO DE BARRAS INTERACTIVO
    const chartCanvas = document.getElementById('kpiChart');
    if (chartCanvas) {
        const ctx       = chartCanvas.getContext('2d');
        const dataPend  = {{ (int)($kpi['pendientes']  ?? 0) }};
        const dataProc  = {{ (int)($kpi['procesados']  ?? 0) }};
        const dataAnul  = {{ (int)($kpi['anulados']    ?? 0) }};
        const total     = dataPend + dataProc + dataAnul;

        // Plugin manual para mostrar valores/porcentajes en cada barra
        const dataLabelsPlugin = {
            id: 'dataLabelsPlugin',
            afterDatasetsDraw(chart, args, options) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    chart.getDatasetMeta(i).data.forEach((bar, index) => {
                        const val = dataset.data[index];
                        if (val === 0) return; // Omitir texto si es 0

                        const pct = total > 0 ? ((val / total) * 100).toFixed(1) + '%' : '';
                        const text = `${val.toLocaleString('es-CO')} (${pct})`;

                        ctx.save();
                        ctx.font = "bold 11px Inter, sans-serif";
                        ctx.fillStyle = "#64748b"; // Gris muted
                        ctx.textAlign = "center";
                        ctx.textBaseline = "bottom";
                        ctx.fillText(text, bar.x, bar.y - 6);
                        ctx.restore();
                    });
                });
            }
        };

        const baseOpts = {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 25 } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", weight: '600' } }
                },
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: '#e2e8f0' },
                    ticks: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                    displayColors: false,
                    titleFont: { family: "'Inter',sans-serif", weight: '700' },
                    bodyFont : { family: "'Inter',sans-serif" },
                    callbacks: {
                        label: ctxInfo => {
                            const val = ctxInfo.raw;
                            const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                            return ` Total: ${val.toLocaleString('es-CO')} (${pct}%)`;
                        }
                    }
                }
            },
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const estadosMap = ['PENDIENTE', 'PROCESADO', 'ANULADO'];
                    const selectedEstado = estadosMap[index];

                    const formFilt = document.getElementById('formFiltros');
                    if (formFilt) {
                        const selectEstado = formFilt.querySelector('select[name="estado"]');
                        if (selectEstado) {
                            selectEstado.value = selectedEstado;
                            formFilt.submit();
                        }
                    }
                }
            },
            animation: { duration: 900, easing: 'easeInOutQuart' }
        };

        if (total === 0) {
            new Chart(ctx, {
                type: 'bar',
                data: { labels: ['Sin datos'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderRadius: 6 }] },
                options: { ...baseOpts, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
            });
        } else {
            new Chart(ctx, {
                type: 'bar',
                plugins: [dataLabelsPlugin],
                data: {
                    labels: ['Pendientes', 'Procesados', 'Anulados'],
                    datasets: [{
                        data            : [dataPend, dataProc, dataAnul],
                        backgroundColor : ['#f59e0b', '#10b981', '#ef4444'],
                        hoverBackgroundColor: ['#d97706', '#059669', '#dc2626'],
                        borderRadius    : 6,
                        borderSkipped   : false,
                        barPercentage   : 0.65
                    }]
                },
                options: baseOpts
            });
        }
    }

    // 4. DRAG & DROP + UPLOAD UX
    const fileInput   = document.getElementById('archivoExcelInput');
    const dropWrapper = document.getElementById('dropWrapper');
    const dropZone    = document.getElementById('dropZoneVisual');
    const dropIcon    = document.getElementById('dropIcon');
    const dropLabel   = document.getElementById('dropLabel');
    const dropMeta    = document.getElementById('dropMeta');
    const uploadCard  = document.getElementById('uploadCard');
    const uploadTitle = document.getElementById('uploadTitle');
    const uploadDesc  = document.getElementById('uploadDesc');
    const btnUpload   = document.getElementById('btnUploadSubmit');
    const formUpload  = document.getElementById('formCargaMasiva');

    if (fileInput && dropWrapper && formUpload) {
        ['dragenter','dragover','dragleave','drop'].forEach(ev =>
            dropWrapper.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false)
        );
        ['dragenter','dragover'].forEach(ev =>
            dropWrapper.addEventListener(ev, () => dropZone.classList.add('is-over'), false)
        );
        ['dragleave','drop'].forEach(ev =>
            dropWrapper.addEventListener(ev, () => dropZone.classList.remove('is-over'), false)
        );
        dropWrapper.addEventListener('drop', e => {
            const files = e.dataTransfer?.files;
            if (files?.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, false);

        fileInput.addEventListener('change', function () {
            if (!this.files?.length) return;
            const file  = this.files[0];
            const sizeMB = (file.size / 1048576).toFixed(2);
            const ext   = file.name.split('.').pop().toUpperCase();
            const allowed = ['XLSX','XLS','CSV'];

            if (!allowed.includes(ext)) {
                alert(`⚠️ Formato .${ext} no soportado.\nUsa: .xlsx, .xls o .csv`);
                this.value = ''; return;
            }
            dropZone.classList.add('is-ready');
            dropIcon.className        = 'fas fa-check-circle fs-2 mb-2';
            dropIcon.style.color      = '#10b981';
            dropLabel.innerHTML       = `<strong style="color:#065f46;">${file.name}</strong>`;
            dropMeta.textContent      = `${sizeMB} MB · ${ext} · Listo para enviar`;

            uploadCard.style.backgroundColor = '#f0fdf4';
            uploadCard.style.borderColor      = '#86efac';
            uploadTitle.innerHTML             = `<i class="fas fa-rocket me-2" style="color:#16a34a;"></i>Archivo seleccionado`;
            uploadTitle.style.color           = '#166534';
            uploadDesc.innerHTML              = 'Revisa nombre y tamaño, luego haz clic en <strong>Subir Lote</strong>.';
            uploadDesc.style.color            = '#15803d';

            btnUpload.disabled                = false;
            btnUpload.setAttribute('aria-disabled', 'false');
            btnUpload.style.opacity           = '1';
            btnUpload.style.pointerEvents     = 'auto';
            btnUpload.className               = 'btn-g btn-primary-g fw-bold';
            btnUpload.querySelector('.btn-ico').className    = 'fas fa-cogs btn-ico';
            btnUpload.querySelector('.btn-label').textContent = 'Subir Lote';
        });

        formUpload.addEventListener('submit', () => {
            btnUpload.classList.add('is-loading');
            btnUpload.disabled             = true;
            uploadCard.style.opacity       = '0.6';
            uploadCard.style.pointerEvents = 'none';
        });
    }

    // 5. SPINNER EN BOTÓN DE INYECCIÓN
    const formInj  = document.getElementById('formInyeccion');
    const btnInj   = document.getElementById('btnInyectar');
    if (formInj && btnInj) {
        formInj.addEventListener('submit', e => {
            if (formInj.checkValidity()) {
                btnInj.classList.add('is-loading');
                btnInj.disabled = true;
            }
        });
    }

    // 6. BÚSQUEDA CON DEBOUNCE (600ms)
    const inputBusq  = document.getElementById('inputBusqueda');
    const formFilt   = document.getElementById('formFiltros');
    let   searchTimer = null;

    if (inputBusq && formFilt) {
        inputBusq.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => formFilt.submit(), 600);
        });
    }

    // 7. PROGRESS BAR
    const progressFill = document.getElementById('progressFill');
    if (progressFill) {
        const targetWidth = progressFill.style.width;
        progressFill.style.width = '0%';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                progressFill.style.width = targetWidth;
            });
        });
    }

    // 8. ANIMACIÓN FLIP CARD EN SIDEBAR FIJO
    const sidebarFlipCard = document.getElementById('sidebarFlipCard');
    const btnFlipToForm   = document.getElementById('btnFlipToForm');
    const btnFlipToList   = document.getElementById('btnFlipToList');

    if (sidebarFlipCard && btnFlipToForm && btnFlipToList) {
        btnFlipToForm.addEventListener('click', () => {
            sidebarFlipCard.classList.add('is-flipped');
        });
        btnFlipToList.addEventListener('click', () => {
            sidebarFlipCard.classList.remove('is-flipped');
        });
    }

    // 9. ACORDEÓN PARA LOS AÑOS Y RESALTE INTELIGENTE
    document.querySelectorAll('.year-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('is-open');
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            // toggle display
            if(content.style.display === 'none') {
                content.style.display = 'flex';
            } else {
                content.style.display = 'none';
            }
        });
    });

    // Auto-abrir el año que contiene el bloque que actualmente estamos viendo
    document.querySelectorAll('.year-content').forEach(content => {
        if(content.querySelector('.active-block')) {
            content.style.display = 'flex';
            const btnId = content.id.replace('year-content-', 'year-toggle-');
            const btn = document.getElementById(btnId);
            if(btn) {
                btn.classList.add('is-open');
                const icon = btn.querySelector('.fa-folder');
                if(icon) { icon.classList.remove('fa-folder'); icon.classList.add('fa-folder-open'); }
            }
        } else {
            content.style.display = 'none';
        }
    });

});
</script>
