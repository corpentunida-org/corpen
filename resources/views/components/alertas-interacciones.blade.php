{{--
    Campanita de "Alertas de Interacciones" — independiente de la de Centro de Soportes (arriba
    en el header), para no mezclar los dos dominios. Dos pestañas: Vencidas (ya pasó la fecha de
    la próxima acción) y Pendientes (sigue abierta, todavía a tiempo). Alimentada por
    InteractionController::alertas() (misma definición de "vencida"/"pendiente" que el correo
    diario y el informe semanal, ver AlertasInteraccionesService).

    Además dispara un aviso (SweetAlert2, ya cargado globalmente en el layout) cada 3 horas
    mientras la persona tenga la pestaña abierta, recordándole las vencidas — el correo diario
    cubre el caso de que no tenga el navegador abierto.
--}}
@candirect('menu.interacciones')
    <div class="dropdown nxl-h-item" id="alertasInteraccionesComponent">
        <div class="nxl-head-link me-3 position-relative" data-bs-toggle="dropdown" role="button"
            data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" id="alertasIntButton"
            title="Alertas de Interacciones">
            <i class="feather-alert-triangle alertas-int-bell"></i>
            <span class="badge bg-danger nxl-h-badge d-none" id="alertasIntBadge" aria-label="Alertas"></span>
        </div>

        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown alertas-int-panel" aria-labelledby="alertasIntButton">
            <div class="alertas-int-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="min-width: 0;">
                        <h6 class="fw-bold mb-0">Alertas de Interacciones</h6>
                        <div class="subtitulo">Próximas acciones vencidas y pendientes</div>
                    </div>
                    <button class="btn btn-sm btn-icon flex-shrink-0 ms-2" id="alertasIntRefresh" title="Actualizar" aria-label="Actualizar">
                        <i class="feather-refresh-cw"></i>
                    </button>
                </div>
                <div class="alertas-int-tabs" role="tablist">
                    <div class="alertas-int-tab active" data-tab="vencidas" role="tab" aria-selected="true">
                        <i class="feather-alert-triangle"></i>
                        <span>Vencidas</span>
                        <span class="alertas-int-count" id="alertasIntCountVencidas">0</span>
                    </div>
                    <div class="alertas-int-tab" data-tab="pendientes" role="tab" aria-selected="false">
                        <i class="feather-clock"></i>
                        <span>Pendientes</span>
                        <span class="alertas-int-count" id="alertasIntCountPendientes">0</span>
                    </div>
                </div>
            </div>

            <div id="alertasIntLista" class="alertas-int-lista">
                <div class="text-center text-muted p-4 fs-13">Cargando...</div>
            </div>

            <div class="text-center alertas-int-footer">
                <a href="{{ route('interactions.index') }}" class="fs-13 fw-semibold text-dark text-decoration-none">
                    Ver Mis Interacciones <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <style>
        .alertas-int-bell { transition: transform .2s ease; }
        .alertas-int-bell:hover { transform: scale(1.05); }

        /* Pulso de atención — cada tantos minutos (configurable), mientras haya algo vencido o
           por vencer hoy, el ícono crece y se pone más rojo por un momento, sin interrumpir nada
           (a diferencia del modal forzado, esto no bloquea ni exige elegir). */
        @keyframes alertasIntPulso {
            0%, 100% { transform: scale(1); color: inherit; }
            25% { transform: scale(1.35); color: #dc2626; }
            50% { transform: scale(1); color: #dc2626; }
            75% { transform: scale(1.2); color: #dc2626; }
        }
        .alertas-int-bell.pulso-atencion {
            animation: alertasIntPulso 1.4s ease-in-out 2;
        }

        /* Mismo tamaño en escritorio y en celular (440px) — en escritorio hay espacio de sobra
           así que no tiene sentido que quede más angosto que la versión mobile.
           !important porque el tema trae ".nxl-header .header-wrapper .nxl-h-dropdown{width:
           225px}" (selector de 3 clases, más específico que ".alertas-int-panel" solo) — sin
           esto, ESE 225px es el que de verdad se estaba aplicando todo este tiempo, no los
           tamaños que se habían ido subiendo antes. Es la causa real de que se siguiera viendo
           angosto pese a los cambios anteriores. */
        .alertas-int-panel {
            width: 440px !important;
            max-height: 520px !important;
            border-radius: 14px;
            border: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            overflow: hidden;
            padding: 0;
        }

        /* En celular se ajusta al ancho real de pantalla (con margen a los lados) sin pasar de
           esos mismos 440px — así nunca queda más angosto que su propio contenido (esa era la
           causa de que la pestaña "Pendientes" se viera cortada: el texto no cabía y el panel,
           con overflow:hidden, lo recortaba en vez de mostrarlo). */
        @media (max-width: 576px) {
            .alertas-int-panel {
                width: calc(100vw - 24px) !important;
                max-width: 440px !important;
                max-height: 70vh !important;
            }
        }

        /* Banner con color propio (no gris) — mismo patrón que el informe de referencia:
           degradado oscuro de cabecera + tarjetas claras debajo. Aquí en tonos rojo/naranja
           porque este panel es, por definición, de urgencias (vencidas/pendientes). */
        .alertas-int-header {
            padding: 16px 16px 14px 16px;
            background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 55%, #c2410c 100%);
            color: #fff;
        }
        .alertas-int-header h6 { color: #fff; }
        .alertas-int-header .subtitulo { font-size: 11.5px; color: rgba(255,255,255,.75); margin-top: 2px; }

        .alertas-int-header .btn-icon {
            background: rgba(255,255,255,.12); border: none; padding: 5px 7px; border-radius: 6px; color: #fff;
        }
        .alertas-int-header .btn-icon:hover { background: rgba(255,255,255,.25); color: #fff; }
        .alertas-int-header .btn-icon.spinning i { animation: alertasIntSpin 1s linear infinite; }
        @keyframes alertasIntSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* flex:1 en cada pestaña (ver abajo) reparte el ancho disponible en dos mitades
           iguales — así las dos pestañas SIEMPRE caben dentro del panel sin desbordarse, sin
           importar el ancho de pantalla. */
        .alertas-int-tabs { display: flex; gap: 8px; margin-top: 12px; }

        /* Semáforo: rojo = vencido, amarillo = pendiente (a tiempo). Colores sólidos y
           saturados a propósito — no pastel — para que se lea como semáforo real. */
        .alertas-int-tab {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            flex: 1 1 0; min-width: 0;
            padding: 7px 10px; border-radius: 20px;
            background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.25);
            cursor: pointer; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,.85);
            white-space: nowrap;
            transition: all .2s ease;
        }
        .alertas-int-tab span:not(.alertas-int-count) { overflow: hidden; text-overflow: ellipsis; }
        .alertas-int-tab i { font-size: 13px; flex-shrink: 0; }
        .alertas-int-tab:hover { transform: translateY(-1px); background: rgba(255,255,255,.22); }
        .alertas-int-tab.active { color: #fff; border-color: transparent; box-shadow: 0 2px 6px rgba(0,0,0,.2); }
        .alertas-int-tab[data-tab="vencidas"].active { background: #dc2626; }
        .alertas-int-tab[data-tab="pendientes"].active { background: #d97706; }

        .alertas-int-count {
            background: rgba(255,255,255,.25); color: #fff; font-size: 10.5px; font-weight: 700;
            padding: 1px 6px; border-radius: 10px; min-width: 16px; text-align: center;
            flex-shrink: 0;
        }

        /* Pantallas muy angostas: un poco más compacto para que quepa cómodo sin recortarse. */
        @media (max-width: 360px) {
            .alertas-int-tab { font-size: 11.5px; padding: 6px 8px; gap: 4px; }
        }

        .alertas-int-lista { max-height: 320px; overflow-y: auto; padding: 10px; background: #fafafa; }
        .alertas-int-lista::-webkit-scrollbar { width: 6px; }
        .alertas-int-lista::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 3px; }

        .alertas-int-item {
            display: block; text-decoration: none; color: inherit;
            padding: 10px 12px; margin-bottom: 6px; border-radius: 8px;
            background: white; border: 1px solid #f1f1f1; border-left: 4px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,.03);
            transition: all .15s ease;
        }
        .alertas-int-item:hover { background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.08); transform: translateX(2px); color: inherit; }
        /* Rojo = vencido, amarillo = pendiente — mismo semáforo del header/pestañas. */
        .alertas-int-item.venc { border-left-color: #dc2626; }
        .alertas-int-item.pend { border-left-color: #d97706; }

        .alertas-int-cliente { font-size: 13px; font-weight: 600; color: #212529; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .alertas-int-fecha { font-size: 11.5px; color: #6c757d; margin-top: 2px; }
        .alertas-int-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 8px; margin-left: 4px; }
        .alertas-int-badge.venc { background: #dc2626; color: #fff; }
        .alertas-int-badge.pend { background: #d97706; color: #fff; }

        .alertas-int-footer { padding: 10px; background: #fff; border-top: 1px solid #f1f1f1; }

        /* Verde = "todo OK", el tercer color del semáforo: sin nada vencido/pendiente en esa
           pestaña no hay nada urgente que mostrar. */
        .alertas-int-empty { text-align: center; padding: 30px 15px; color: #15803d; }
        .alertas-int-empty i { font-size: 1.8rem; }
        .alertas-int-empty p { margin: 8px 0 0 0; font-size: 12.5px; font-weight: 600; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const badge = document.getElementById('alertasIntBadge');
            const lista = document.getElementById('alertasIntLista');
            const countVencidas = document.getElementById('alertasIntCountVencidas');
            const countPendientes = document.getElementById('alertasIntCountPendientes');
            const refreshBtn = document.getElementById('alertasIntRefresh');
            const tabs = document.querySelectorAll('.alertas-int-tab');

            // El intervalo ya NO está fijo en 3 horas — viene de la respuesta del servidor
            // (aviso_intervalo_horas), configurable en Admin → Configuración de Alertas de
            // Interacciones. 3h es solo el valor por defecto mientras llega la primera carga.
            let AVISO_INTERVALO_MS = 3 * 60 * 60 * 1000;
            const LS_KEY = 'daytrack_ultimo_aviso_vencidas';
            const LS_KEY_MATUTINO = 'daytrack_recordatorio_matutino_fecha';

            let recordatorioMatutinoHora = '08:00';
            let pulsoIntervaloMs = 10 * 60 * 1000;
            let ultimoPulso = 0;

            let datos = { vencidas: [], pendientes: [], venceHoy: [] };
            let tabActual = 'vencidas';
            let pantallaOmitidaActual = false;

            function cargarAlertas(mostrarAvisoSiToca) {
                return fetch('{{ route('interactions.alertas.index') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        datos.vencidas = data.vencidas || [];
                        datos.pendientes = data.pendientes || [];
                        datos.venceHoy = data.vence_hoy || [];

                        if (data.aviso_intervalo_horas) {
                            AVISO_INTERVALO_MS = data.aviso_intervalo_horas * 60 * 60 * 1000;
                        }
                        if (data.recordatorio_matutino_hora) recordatorioMatutinoHora = data.recordatorio_matutino_hora;
                        if (data.pulso_intervalo_minutos) pulsoIntervaloMs = data.pulso_intervalo_minutos * 60 * 1000;

                        countVencidas.textContent = data.vencidas_count ?? 0;
                        countPendientes.textContent = data.pendientes_count ?? 0;

                        const totalUrgente = data.vencidas_count ?? 0;
                        if (totalUrgente > 0) {
                            badge.textContent = totalUrgente > 99 ? '99+' : totalUrgente;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }

                        renderLista();
                        // Si está en la lista de "omitir pantalla" (Admin → Configuración de
                        // Alertas → Agentes Omitidos), no se le fuerza NADA visual/sonoro en
                        // pantalla — ni el modal cada N horas, ni el recordatorio matutino, ni
                        // el pulso del ícono. Sigue viendo su campanita normalmente si quiere.
                        pantallaOmitidaActual = !!data.pantalla_omitida;
                        if (mostrarAvisoSiToca && !data.pantalla_omitida) {
                            evaluarAvisoPeriodico(totalUrgente);
                            evaluarRecordatorioMatutino(data.vence_hoy_count ?? 0);
                        }
                        return data;
                    })
                    .catch(err => console.error('Error cargando alertas de interacciones:', err));
            }

            function renderLista() {
                const items = datos[tabActual] || [];
                const claseTipo = tabActual === 'vencidas' ? 'venc' : 'pend';

                if (items.length === 0) {
                    const mensaje = tabActual === 'vencidas' ? 'No tienes interacciones vencidas' : 'No tienes interacciones pendientes';
                    lista.innerHTML = `
                        <div class="alertas-int-empty">
                            <i class="feather-check-circle"></i>
                            <p>${mensaje}</p>
                        </div>`;
                    return;
                }

                lista.innerHTML = items.map(item => {
                    const badgeHtml = (claseTipo === 'venc' && item.dias_vencida !== null)
                        ? `<span class="alertas-int-badge venc">${item.dias_vencida} día(s) vencida</span>`
                        : (item.next_action_date ? `<span class="alertas-int-badge pend">a tiempo</span>` : '');

                    return `
                        <a href="${item.url}" class="alertas-int-item ${claseTipo}">
                            <div class="alertas-int-cliente">${item.cliente}</div>
                            <div class="alertas-int-fecha">
                                ${item.next_action_date ? ('Próxima acción: ' + item.next_action_date) : 'Sin fecha de próxima acción'}
                                ${badgeHtml}
                            </div>
                        </a>`;
                }).join('');
            }

            // Registra la decisión (responder/posponer) en el servidor — ver
            // AlertasInteraccionesService::registrarDecision(). Devuelve {streak, escalado}.
            function registrarDecision(decision) {
                return fetch('{{ route('interactions.alertas.decision') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ decision }),
                }).then(r => r.json()).catch(err => {
                    console.error('Error registrando decisión de alerta:', err);
                    return { streak: null, escalado: false };
                });
            }

            function evaluarAvisoPeriodico(countVencidasNum) {
                if (countVencidasNum <= 0) return;

                let ultimoAviso = 0;
                try {
                    ultimoAviso = parseInt(localStorage.getItem(LS_KEY) || '0', 10);
                } catch (e) { /* almacenamiento no disponible (modo privado, etc.) — se sigue igual */ }

                const ahora = Date.now();
                if (ahora - ultimoAviso >= AVISO_INTERVALO_MS) {
                    // Si ya hay un modal forzado abierto (ej. el de Soportes, que corre en
                    // paralelo e independiente) NO se dispara este encima — dos Swal.fire() casi
                    // al mismo tiempo se pisan entre sí a medio renderizar y el botón de
                    // "Posponer" puede quedar roto. Sin marcar el aviso como mostrado, así que
                    // se reintenta en el siguiente sondeo (5 min) en vez de perder el turno.
                    if (typeof Swal !== 'undefined' && Swal.isVisible && Swal.isVisible()) {
                        return;
                    }
                    if (typeof Swal !== 'undefined') {
                        // Modal "de esta misma ventana" (mismo degradado rojo) que NO se puede
                        // cerrar haciendo clic afuera ni con Esc — obliga a elegir un botón.
                        const itemsHtml = (datos.vencidas || []).slice(0, 5).map(item => `
                            <div style="text-align:left; padding:8px 10px; margin-bottom:6px; background:#fff; border:1px solid #f1f1f1; border-left:3px solid #dc2626; border-radius:6px;">
                                <div style="font-size:13px; font-weight:600; color:#212529;">${item.cliente}</div>
                                <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">
                                    Vencía: ${item.next_action_date ?? '—'}
                                    ${item.dias_vencida !== null ? `<span style="color:#dc2626; font-weight:700;"> · ${item.dias_vencida} día(s)</span>` : ''}
                                </div>
                            </div>`).join('');
                        const masTexto = countVencidasNum > 5 ? `<div style="text-align:center; font-size:12px; color:#6c757d;">y ${countVencidasNum - 5} más...</div>` : '';

                        Swal.fire({
                            html: `
                                <div style="margin:-20px -24px 16px -24px; padding:16px 24px 14px 24px; background:linear-gradient(135deg,#7f1d1d 0%,#b91c1c 55%,#c2410c 100%); color:#fff; border-radius:8px 8px 0 0;">
                                    <div style="font-weight:700; font-size:16px;">Alertas de Interacciones</div>
                                    <div style="font-size:12px; opacity:.85; margin-top:2px;">Tienes ${countVencidasNum} interacción(es) vencida(s) — decide qué hacer</div>
                                </div>
                                <div style="max-height:220px; overflow-y:auto; padding:0 2px;">${itemsHtml}${masTexto}</div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Responder Ahora',
                            confirmButtonColor: '#16a34a',
                            showDenyButton: true,
                            denyButtonText: 'Posponer',
                            denyButtonColor: '#d97706',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showCloseButton: false,
                            width: 420,
                            padding: '20px 24px 24px 24px',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                registrarDecision('responder');
                                // Si hay una sola vencida, va directo a su pantalla de gestión
                                // (edición, con cronómetro) — no tiene sentido mandarla al
                                // listado general cuando ya se sabe exactamente cuál es.
                                // Con varias, no hay una sola a la cual ir, así que cae al
                                // listado (ya queda filtrado a lo propio).
                                const vencidasActuales = datos.vencidas || [];
                                window.location.href = vencidasActuales.length === 1
                                    ? vencidasActuales[0].url
                                    : '{{ route('interactions.index') }}';
                                return;
                            }
                            if (result.isDenied) {
                                registrarDecision('posponer').then((r) => {
                                    let mensaje = 'Te lo recordaremos en 3 horas.';
                                    let icono = 'info';
                                    if (r.escalado) {
                                        mensaje = `Llevas ${r.streak} días seguidos posponiendo — se notificó a tu administrador de área.`;
                                        icono = 'warning';
                                    } else if (r.streak >= 2) {
                                        mensaje = `Llevas ${r.streak} días seguidos posponiendo. Si llegas a 3, se notifica a tu administrador de área.`;
                                        icono = 'warning';
                                    }
                                    Swal.fire({
                                        icon: icono,
                                        title: 'Pospuesto',
                                        text: mensaje,
                                        timer: 5000,
                                        timerProgressBar: true,
                                        confirmButtonText: 'Entendido',
                                        confirmButtonColor: '#6c757d',
                                    });
                                });
                            }
                        });
                    }
                    try {
                        localStorage.setItem(LS_KEY, String(ahora));
                    } catch (e) { /* ignorar si no hay almacenamiento disponible */ }
                }
            }

            // Sonido corto (Web Audio API, sin archivo externo) — mismo enfoque que ya usa
            // Centro de Soportes. Los navegadores bloquean el autoplay de audio hasta que haya
            // habido alguna interacción de la persona con la página en esa sesión; si el
            // navegador lo rechaza, se ignora en silencio (no rompe nada).
            function reproducirSonido(frecuencia, duracionMs) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = frecuencia;
                    gain.gain.value = 0.12;
                    osc.start();
                    osc.stop(ctx.currentTime + duracionMs / 1000);
                } catch (e) { /* audio no disponible/bloqueado — se ignora */ }
            }

            // Recordatorio matutino: UNA vez al día (se compara por fecha, no por intervalo),
            // al abrir/recargar la app después de la hora configurada, si hay algo que vence
            // HOY (todavía no vencido — eso ya lo cubre el aviso cada N horas). No es forzado:
            // se puede cerrar con normalidad, solo suena y muestra la lista.
            function evaluarRecordatorioMatutino(countVenceHoy) {
                if (countVenceHoy <= 0) return;
                if (typeof Swal === 'undefined') return;

                const hoy = new Date();
                const fechaHoy = hoy.toISOString().slice(0, 10);

                let yaMostradoHoy = null;
                try {
                    yaMostradoHoy = localStorage.getItem(LS_KEY_MATUTINO);
                } catch (e) { /* almacenamiento no disponible — se sigue igual */ }
                if (yaMostradoHoy === fechaHoy) return;

                const [horaCfg, minCfg] = recordatorioMatutinoHora.split(':').map(Number);
                const horaActualEnMinutos = hoy.getHours() * 60 + hoy.getMinutes();
                const horaCfgEnMinutos = (horaCfg || 0) * 60 + (minCfg || 0);
                if (horaActualEnMinutos < horaCfgEnMinutos) return;

                const itemsHtml = (datos.venceHoy || []).slice(0, 5).map(item => `
                    <div style="text-align:left; padding:8px 10px; margin-bottom:6px; background:#fff; border:1px solid #f1f1f1; border-left:3px solid #d97706; border-radius:6px;">
                        <div style="font-size:13px; font-weight:600; color:#212529;">${item.cliente}</div>
                        <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">Hoy: ${item.next_action_date ?? '—'}</div>
                    </div>`).join('');
                const masTexto = countVenceHoy > 5 ? `<div style="text-align:center; font-size:12px; color:#6c757d;">y ${countVenceHoy - 5} más...</div>` : '';

                reproducirSonido(880, 0.15);
                setTimeout(() => reproducirSonido(1046, 0.15), 180);

                Swal.fire({
                    html: `
                        <div style="margin:-20px -24px 16px -24px; padding:16px 24px 14px 24px; background:linear-gradient(135deg,#7f1d1d 0%,#c2410c 55%,#d97706 100%); color:#fff; border-radius:8px 8px 0 0;">
                            <div style="font-weight:700; font-size:16px;">☀️ Buenos días</div>
                            <div style="font-size:12px; opacity:.9; margin-top:2px;">Tienes ${countVenceHoy} interacción(es) que vencen HOY</div>
                        </div>
                        <div style="max-height:220px; overflow-y:auto; padding:0 2px;">${itemsHtml}${masTexto}</div>
                    `,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#c2410c',
                    width: 420,
                    padding: '20px 24px 24px 24px',
                });

                try {
                    localStorage.setItem(LS_KEY_MATUTINO, fechaHoy);
                } catch (e) { /* ignorar si no hay almacenamiento disponible */ }
            }

            // Pulso de atención en el ícono — cada tantos minutos (configurable), mientras haya
            // vencidas o algo por vencer hoy, el ícono se anima y suena un blip corto. No
            // interrumpe nada (no es un modal), solo llama la atención de reojo.
            function evaluarPulsoAtencion(totalUrgente, pantallaOmitida) {
                if (pantallaOmitida || totalUrgente <= 0) return;

                const ahora = Date.now();
                if (ultimoPulso !== 0 && ahora - ultimoPulso < pulsoIntervaloMs) return;
                ultimoPulso = ahora;

                const icono = document.querySelector('.alertas-int-bell');
                if (icono) {
                    icono.classList.remove('pulso-atencion');
                    void icono.offsetWidth; // fuerza el reinicio de la animación si ya estaba
                    icono.classList.add('pulso-atencion');
                    setTimeout(() => icono.classList.remove('pulso-atencion'), 3000);
                }
                reproducirSonido(660, 0.12);
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
                    tab.classList.add('active');
                    tab.setAttribute('aria-selected', 'true');
                    tabActual = tab.getAttribute('data-tab');
                    renderLista();
                });
            });

            refreshBtn.addEventListener('click', () => {
                refreshBtn.classList.add('spinning');
                cargarAlertas(false).finally(() => setTimeout(() => refreshBtn.classList.remove('spinning'), 600));
            });

            // Carga inicial y refresco cada 5 minutos revisando también si ya toca el aviso.
            cargarAlertas(true);
            setInterval(() => cargarAlertas(true), 5 * 60 * 1000);

            // El pulso del ícono se revisa aparte, cada 30s, con los últimos datos ya cargados
            // (sin pedirle nada nuevo al servidor) — así el intervalo configurado (que puede ser
            // más corto que los 5 minutos del sondeo de datos) se respeta de verdad.
            setInterval(() => {
                const totalUrgente = (datos.vencidas?.length || 0) + (datos.venceHoy?.length || 0);
                evaluarPulsoAtencion(totalUrgente, pantallaOmitidaActual);
            }, 30 * 1000);
        });
    </script>
@endcandirect
