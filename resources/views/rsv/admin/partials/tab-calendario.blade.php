{{-- resources/views/rsv/admin/partials/tab-calendario.blade.php --}}
{{-- ESTILO: hoja de cálculo SUAVE · encabezados 9px · eventos 8px · pasteles --}}

<div id="calRoot">

    @php
        $listaInmuebles = $inmuebles ?? collect();
    @endphp

    <!-- ═══ CINTA SUPERIOR: leyenda + filtro ═══ -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-2 py-1.5 mb-2"
         style="background: #fafbfd; border: 1px solid #f1f5f9; border-radius: 4px 4px 0 0;">

        <!-- Leyenda pastel -->
        <div class="d-flex flex-wrap align-items-center gap-1">

            <span class="me-1" style="font-size: 7.5px; letter-spacing: .05em; color: #b3bcc7; font-weight: 600;">
                <i class="bi bi-calendar3 me-1" style="font-size: 8px;"></i>LEYENDA
            </span>

            <span class="cal-chip"><span class="ldot" style="background:#a3cfb6;"></span> Aprobada</span>
            <span class="cal-chip"><span class="ldot" style="background:#ecd3a0;"></span> Pendiente</span>
            <span class="cal-chip"><span class="ldot" style="background:#a9cfe8;"></span> Endoso</span>
            <span class="cal-chip"><span class="ldot" style="background:#e5b8b8;"></span> Cancelada</span>
            <span class="cal-chip"><span class="ldot" style="background:#c98a85;"></span> Mantenimiento</span>

        </div>

        <!-- Filtro de inmueble (suave) -->
        <div class="d-flex align-items-center gap-1">
            <i class="bi bi-building" style="font-size: 9px; color: #c3ccd6;"></i>
            <select id="filtro_inmueble_cal" class="cal-select" title="Filtrar por inmueble">
                <option value="">Todos los inmuebles</option>
                @foreach($listaInmuebles as $inm)
                    <option value="{{ $inm->id }}">{{ $inm->name }}</option>
                @endforeach
            </select>
        </div>

    </div>


    <!-- ═══ MARCO DEL CALENDARIO + LOADER CRISTAL ═══ -->
    <div class="position-relative"
         style="border: 1px solid #f1f5f9; border-radius: 0 0 4px 4px; background: #fff; overflow: hidden;">

        <div id="calendar-loader"
             class="d-none position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
             style="z-index: 50; background: rgba(255,255,255,.72); backdrop-filter: blur(3px);">

            <div class="spinner-border spinner-border-sm" role="status" style="color: #3d7a5c; width: 14px; height: 14px;"></div>

        </div>

        <div id="calendario-global" class="p-1"></div>

    </div>

</div>


@push('style')
<style>
    /* ═══ BASE — anclada a #calRoot (misma paleta de las hojas) ═══ */
    #calRoot {
        --grid:  #f1f5f9;
        --grid-2:#e9eef4;
        --head:  #fafbfd;
        --ink:   #64748b;
        --ink-2: #475569;
        --ink-3: #94a3b8;
        --ink-4: #b3bcc7;
        --accent:#3d7a5c;
        --accent-bg: #f0f7f2;
        --accent-bd: #d5e7dc;
        font-family: Calibri, "Segoe UI", system-ui, sans-serif;
        font-size: 8px;
        line-height: 1.4;
        color: var(--ink);
    }

    /* Chips de leyenda */
    #calRoot .cal-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 1px 8px; border-radius: 9px;
        font-size: 8px; font-weight: 500;
        border: 1px solid var(--grid); background: #fcfdfe; color: var(--ink-3);
    }
    #calRoot .ldot {
        width: 5px; height: 5px; border-radius: 50%;
        display: inline-block; opacity: .85;
    }

    /* Select suave */
    #calRoot .cal-select {
        border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2);
        font-family: inherit; font-size: 9px; font-weight: 500;
        padding: 2px 8px; border-radius: 3px; cursor: pointer;
        box-shadow: none !important; outline: none;
    }
    #calRoot .cal-select:focus { border-color: #c8d6cd; }


    /* ═══ FULLCALENDAR · overrides (scoped) ═══ */

    /* Grilla */
    #calRoot .fc-theme-standard td,
    #calRoot .fc-theme-standard th,
    #calRoot .fc-theme-standard .fc-scrollgrid { border-color: var(--grid); }

    /* Encabezados de día — 9px */
    #calRoot .fc-col-header-cell {
        padding: 4px 0 !important;
        background: var(--head);
        color: var(--ink-3);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: 9px !important;
        border: none !important;
    }
    #calRoot .fc-col-header-cell-cushion { color: inherit; text-decoration: none; padding: 2px 4px; }

    /* Celdas de día — 8px */
    #calRoot .fc-daygrid-day-number {
        font-size: 8px !important;
        padding: 2px 5px !important;
        font-weight: 400;
        color: var(--ink-3);
        text-decoration: none;
    }
    #calRoot .fc-day-today { background: var(--accent-bg) !important; }
    #calRoot .fc-day-today .fc-daygrid-day-number { color: var(--accent); font-weight: 600; }

    #calRoot .fc-dayGridMonth-view .fc-daygrid-day-frame { min-height: 62px; }
    #calRoot .fc-daygrid-day { transition: background .15s ease; }
    #calRoot .fc-daygrid-day:hover { background: #fafcfe; }

    /* Otros meses: aún más tenues */
    #calRoot .fc-day-other .fc-daygrid-day-number { color: #dfe5ec; }

    /* Toolbar — botones estilo soft */
    #calRoot .fc .fc-button {
        background: #fff !important;
        color: var(--ink-3) !important;
        border: 1px solid var(--grid-2) !important;
        box-shadow: none !important;
        font-family: inherit;
        font-size: 9px !important;
        font-weight: 600;
        text-transform: capitalize;
        border-radius: 3px !important;
        padding: 2px 9px !important;
        transition: all .15s ease;
    }
    #calRoot .fc .fc-button:hover {
        background: #fafcfe !important;
        color: var(--ink-2) !important;
        border-color: #d7dee6 !important;
    }
    #calRoot .fc .fc-button:not(:disabled).fc-button-active {
        background: var(--accent-bg) !important;
        color: var(--accent) !important;
        border-color: var(--accent-bd) !important;
    }
    #calRoot .fc .fc-button-group { gap: 3px; }
    #calRoot .fc .fc-toolbar-chunk { display: flex; gap: 4px; align-items: center; }

    /* Título del mes */
    #calRoot .fc-toolbar-title {
        font-size: 10.5px !important;
        font-weight: 600;
        color: var(--ink-2);
        letter-spacing: .01em;
    }

    /* Eventos — 8px, pastel, barra lateral de estado */
    #calRoot .fc-event {
        border-radius: 3px !important;
        padding: 1px 4px !important;
        margin-bottom: 1.5px !important;
        font-size: 8px !important;
        font-weight: 500;
        line-height: 1.3;
        box-shadow: none;
        border-width: 1px !important;
        border-left-width: 3px !important;
        cursor: pointer;
        transition: filter .15s ease;
    }
    #calRoot .fc-event:hover { filter: brightness(.985); }
    #calRoot .fc-event-main, .fc-event-title, .fc-event-time { color: inherit !important; }
    #calRoot .fc-event-title { font-weight: 600; }
    #calRoot .fc-daygrid-event-dot { display: none; }

    /* Estados (franja lateral pastel) */
    #calRoot .ev-cancelada {
        background: #fdf4f4 !important;
        border-color: #f0dcdc !important;
        color: #a87878 !important;
        text-decoration: line-through;
        opacity: .8;
    }
    #calRoot .ev-bloqueo {
        background: #fdf0ef !important;
        border-color: #edcfcf !important;
        color: #965757 !important;
    }

    /* Vista anual (multi-month) */
    #calRoot .fc-multimonth-month {
        border: 1px solid var(--grid);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 8px;
    }
    #calRoot .fc-multimonth-title {
        font-size: 9px !important; font-weight: 600;
        padding: 4px 9px;
        background: var(--head);
        border-bottom: 1px solid var(--grid);
        color: var(--ink-3);
        text-transform: uppercase; letter-spacing: .05em;
    }
    #calRoot .fc-multimonth-view .fc-daygrid-day-frame { min-height: 20px; }
    #calRoot .fc-multimonth-view .fc-event { font-size: 7.5px !important; padding: 0 3px !important; }

    /* Vista semana (timeGrid) */
    #calRoot .fc-timegrid-slot-label,
    #calRoot .fc-timegrid-axis { font-size: 7.5px !important; color: var(--ink-4); }
    #calRoot .fc-timegrid-event { font-size: 7.5px !important; border-radius: 3px; }
    #calRoot .fc-timegrid-col.fc-day-today { background: var(--accent-bg) !important; }
</style>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calEl  = document.getElementById('calendario-global'),
              filtro = document.getElementById('filtro_inmueble_cal'),
              loader = document.getElementById('calendar-loader');

        if (!calEl) return;

        /* Paleta pastel por inmueble (saturación rebajada) */
        const getInmuebleStyle = (inmuebleName) => {
            if (!inmuebleName) return { bg: '#fafbfd', border: '#e2e8f0', text: '#64748b' };

            let hash = 0;
            for (let i = 0; i < inmuebleName.length; i++) {
                hash = inmuebleName.charCodeAt(i) + ((hash << 5) - hash);
            }
            const hue = Math.abs(hash) % 360;

            return {
                bg:     `hsl(${hue}, 45%, 96%)`,
                border: `hsl(${hue}, 35%, 80%)`,
                text:   `hsl(${hue}, 25%, 38%)`
            };
        };

        /* Franjas de estado pastel */
        const STATUS = {
            endoso:     '#8fb8e0',
            pendiente:  '#d9b56a',
            cancelada:  '#c98080',
            bloqueo:    '#c98a85'
        };

        const addDay = (dateStr) => {
            if (!dateStr) return null;
            const clean = dateStr.split(' ')[0];
            const parts = clean.split('-');
            if (parts.length !== 3) return clean;

            const y = parseInt(parts[0]), m = parseInt(parts[1]) - 1, d = parseInt(parts[2]);
            const dObj = new Date(y, m, d + 1);

            const yr = dObj.getFullYear();
            const mo = String(dObj.getMonth() + 1).padStart(2, '0');
            const da = String(dObj.getDate()).padStart(2, '0');
            return `${yr}-${mo}-${da}`;
        };

        const formatCleanDate = (dateStr) => {
            if (!dateStr) return '';
            const clean = dateStr.split(' ')[0];
            const parts = clean.split('-');
            if (parts.length !== 3) return dateStr;

            const year = parts[0];
            const monthIndex = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]);
            const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

            return `${day} de ${meses[monthIndex]} de ${year}`;
        };

        const calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            multiMonthMaxColumns: 3,
            locale: 'es',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: 'multiMonthYear,dayGridMonth,timeGridWeek' },
            buttonText: { year: 'Año', month: 'Mes', week: 'Semana', today: 'Hoy' },

            events: async (info, success, fail) => {
                const fIni = info.startStr.split('T')[0], fFin = info.endStr.split('T')[0];
                const qInm = filtro.value ? `&id_rsv_catalogo_inmueble=${filtro.value}` : '';
                loader.classList.remove('d-none');

                try {
                    const [res, blq] = await Promise.all([
                        fetch(`/rsv/reservas?fecha_desde=${fIni}&fecha_hasta=${fFin}&per_page=2000${qInm}`, { headers: { Accept: 'application/json' } }).then(r => r.json()),
                        fetch(`/rsv/bloqueos-calendario?fecha_desde=${fIni}&fecha_hasta=${fFin}&per_page=2000${qInm}`, { headers: { Accept: 'application/json' } }).then(r => r.json())
                    ]);

                    const arrRes = res.data?.data || res.data || [];
                    const arrBlq = blq.data?.data || blq.data || [];

                    const evts = [
                        ...arrRes.filter(r => r.fecha_inicio && r.fecha_fin).map(r => {
                            let st = (r.status?.nombre || r.status?.name || '').toLowerCase();
                            let endoso = r.historial_endosos?.length || r.historialEndosos?.length;
                            let inmuebleName = r.inmueble?.name || 'Apto';

                            let theme = getInmuebleStyle(inmuebleName);
                            let cssClass = '';
                            let icn = 'bi-check2-circle';
                            let statusBorderColor = theme.border;

                            if (st.includes('cancelad')) {
                                cssClass = 'ev-cancelada';
                                icn = 'bi-x-circle';
                                statusBorderColor = STATUS.cancelada;
                            } else if (endoso || st.includes('endos')) {
                                icn = 'bi-arrow-left-right';
                                statusBorderColor = STATUS.endoso;
                            } else if (st.includes('pendient') || st.includes('pre')) {
                                icn = 'bi-clock-history';
                                statusBorderColor = STATUS.pendiente;
                            }

                            let startRaw = r.fecha_inicio.split(' ')[0];
                            let endRaw = r.fecha_fin.split(' ')[0];

                            return {
                                id: `rsv_${r.id}`,
                                title: `${inmuebleName} (${r.codigo_reserva})`,
                                start: startRaw,
                                end: addDay(endRaw),
                                allDay: true,
                                classNames: [cssClass],
                                backgroundColor: theme.bg,
                                borderColor: theme.border,
                                textColor: theme.text,
                                extendedProps: {
                                    t: 'rsv',
                                    icn,
                                    cod: r.codigo_reserva,
                                    est: st.toUpperCase(),
                                    inm: inmuebleName,
                                    usr: r.user?.name,
                                    realStart: startRaw,
                                    realEnd: endRaw,
                                    statusColor: statusBorderColor
                                }
                            };
                        }),
                        ...arrBlq.filter(b => b.fecha_inicio && b.fecha_fin).map(b => {
                            let startRaw = b.fecha_inicio.split(' ')[0];
                            let endRaw = b.fecha_fin.split(' ')[0];
                            return {
                                id: `blq_${b.id}`,
                                title: `Mtto: ${b.inmueble?.name || ''}`,
                                start: startRaw,
                                end: addDay(endRaw),
                                allDay: true,
                                classNames: ['ev-bloqueo'],
                                extendedProps: {
                                    t: 'blq',
                                    icn: 'bi-tools',
                                    dtl: b.motivo,
                                    inm: b.inmueble?.name,
                                    realStart: startRaw,
                                    realEnd: endRaw,
                                    statusColor: STATUS.bloqueo
                                }
                            };
                        })
                    ];

                    loader.classList.add('d-none');
                    success(evts);
                } catch (e) {
                    console.error("Error cargando eventos:", e);
                    loader.classList.add('d-none'); fail(e);
                }
            },

            eventDidMount: (info) => {
                const stColor = info.event.extendedProps.statusColor;
                if (stColor) {
                    info.el.style.borderLeft = `3px solid ${stColor}`;
                }
            },

            eventContent: (arg) => ({
                html: `<div class="text-truncate">
                          <i class="bi ${arg.event.extendedProps.icn} me-1"></i>${arg.event.title}
                       </div>`
            }),

            eventClick: ({ event }) => {
                const { t, icn, cod, est, usr, dtl, inm, realStart, realEnd } = event.extendedProps;

                Swal.fire({
                    title: t === 'blq' ? 'Mantenimiento' : 'Reserva',
                    html: `
                        <div class="text-start mt-1" style="font-family: Calibri, 'Segoe UI', sans-serif; font-size: 11px; color: #64748b;">
                            <div style="font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 8px;">
                                <i class="bi ${icn}" style="color:#3d7a5c;"></i> ${inm || 'Inmueble'}
                            </div>

                            ${t === 'blq'
                                ? `<div style="margin-bottom:3px;"><b style="color:#475569;">Motivo:</b> ${dtl || '—'}</div>`
                                : `<div style="margin-bottom:3px;"><b style="color:#475569;">Código:</b> ${cod}</div>
                                   <div style="margin-bottom:3px;"><b style="color:#475569;">Estado:</b> ${est}</div>
                                   <div style="margin-bottom:3px;"><b style="color:#475569;">Asociado:</b> ${usr || '—'}</div>`}

                            <hr style="border:none; border-top:1px solid #f1f5f9; margin:8px 0;">

                            <div style="margin-bottom:3px; color:#94a3b8;">
                                <i class="bi bi-box-arrow-in-right"></i> <b>Ingreso:</b> ${formatCleanDate(realStart)}
                            </div>
                            <div style="margin-bottom:0; color:#94a3b8;">
                                <i class="bi bi-box-arrow-left"></i> <b>Salida:</b> ${formatCleanDate(realEnd)}
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#3d7a5c',
                    buttonsStyling: true
                });
            }
        });

        calendar.render();
        filtro.addEventListener('change', () => calendar.refetchEvents());

        const tab = document.querySelector('button[data-bs-target="#calendario"]');
        if (tab) tab.addEventListener('shown.bs.tab', () => calendar.updateSize());
    });
</script>
@endpush
