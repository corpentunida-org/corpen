<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Interacciones - Versión 5 Estrellas (Completa)</title>

  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <!-- ICONS & PLUGINS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Toastr (notificaciones) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

  <!-- NProgress (barra de carga) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" rel="stylesheet" />

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --primary-color: #345B54;
      --primary-hover: #2A4842;
      --accent-color: #8A2BE2;
      --success-color: #28a745;
      --warning-color: #ffc107;
      --danger-color: #dc3545;
      --text-color: #2D3748;
      --text-muted: #718096;
      --background-color: #F8F7F5;
      --card-bg: #FFFFFF;
      --border-color: #E2E8F0;
      --focus-ring: rgba(52, 91, 84, 0.18);
      --cluster-bg: #FDFCFB;
      --shadow-soft: 0 8px 20px rgba(16,24,40,0.06);
      --radius: 12px;
    }

    /* Dark mode */
    @media (prefers-color-scheme: dark) {
      :root{
        --card-bg: #141414;
        --background-color: #0f1720;
        --text-color: #e6eef2;
        --text-muted: #9aa6b2;
        --border-color: rgba(255,255,255,0.06);
        --cluster-bg: #0b1416;
      }
    }

    *{box-sizing:border-box}
    html,body{height:100%;}
    body{
      margin:0;
      font-family:'Inter',sans-serif;
      background:linear-gradient(180deg, #FBFBFA 0%, var(--background-color) 100%);
      color:var(--text-color);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      font-size:16px;
      line-height:1.5;
      padding:1.25rem;
      transition:background .18s ease,color .18s ease;
    }

    /* Container */
    .form-container{
      max-width:1200px;
      margin:0 auto;
      background:var(--card-bg);
      border-radius:16px;
      padding:2rem;
      box-shadow:var(--shadow-soft);
      border:1px solid var(--border-color);
      transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .form-container:focus-within{ transform: translateY(-2px); box-shadow:0 14px 40px rgba(16,24,40,0.12); }

    /* Layout */
    .interaction-layout{ display:grid; grid-template-columns: 2fr 1fr; gap:1.75rem; }
    @media (max-width: 1024px){ .interaction-layout{ grid-template-columns: 1fr; } }

    /* Titles & labels */
    .form-section{ margin-bottom:1.25rem; }
    .form-section-title{ display:flex; align-items:center; gap:.75rem; font-size:1.05rem; font-weight:600; margin-bottom:.8rem; color:var(--primary-color); }
    .form-label{ display:block; font-weight:500; font-size:.9rem; margin-bottom:.4rem; color:var(--text-muted); }

    /* Inputs */
    .form-group{ position:relative; margin-bottom:.75rem; }
    .form-control, .select2-container .select2-selection--single, .select2-container .select2-selection--multiple {
      width:100%;
      padding:.8rem 1rem;
      border-radius:10px;
      border:1px solid var(--border-color);
      font-size:.95rem;
      background:#fff;
      transition:box-shadow .18s ease, border-color .18s ease, transform .12s ease, background .18s ease;
    }
    .form-control:focus, .select2-container--default .select2-selection--single:focus{
      outline:none;
      border-color:var(--primary-color);
      box-shadow:0 6px 20px var(--focus-ring);
      transform:translateY(-1px);
    }
    textarea.form-control{ min-height:140px; resize:vertical; }

    /* validation */
    .helper-error{ color:var(--danger-color); font-size:.85rem; margin-top:.35rem; display:none; }
    .helper-success{ color:var(--success-color); font-size:.85rem; margin-top:.35rem; display:none; }
    .is-valid{ border-color:var(--success-color) !important; box-shadow:0 6px 18px rgba(40,167,69,0.08) !important; }
    .is-invalid{ border-color:var(--danger-color) !important; box-shadow:0 6px 18px rgba(220,53,69,0.06) !important; }
    .valid-icon{ position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:var(--success-color); opacity:0; transition:opacity .12s ease; pointer-events:none; }
    .is-valid + .valid-icon{ opacity:1; }

    /* cluster */
    .form-group-cluster{ background:var(--cluster-bg); border:1px solid var(--border-color); padding:1rem; border-radius:12px; }

    /* buttons */
    .btn{ display:inline-flex; align-items:center; gap:.6rem; padding:.65rem 1rem; border-radius:10px; border:1px solid transparent; cursor:pointer; font-weight:600; }
    .btn-primary{ background:var(--primary-color); color:#fff; box-shadow: 0 6px 14px rgba(52,91,84,0.08); }
    .btn-primary:hover{ background:var(--primary-hover); transform:translateY(-2px); }
    .btn-secondary{ background:#fff; border:1px solid var(--border-color); color:var(--text-muted) }
    .btn-quick-date{ background:#F0F2F5; border:1px solid var(--border-color); padding:.35rem .7rem; border-radius:8px; cursor:pointer; font-size:.85rem; }

    /* sidebar client card */
    .client-info-card{ display:none; padding:1rem; border-radius:12px; border:1px solid var(--border-color); background:linear-gradient(180deg,#fff,#fbfbfc); }
    .client-avatar{ width:64px; height:64px; border-radius:50%; background:var(--primary-color); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:700; margin:0 auto 0.6rem; box-shadow:0 6px 18px rgba(52,91,84,0.08); }
    .client-name{ text-align:center; font-weight:700; color:var(--primary-color); margin-bottom:.25rem; }
    .info-item{ display:flex; align-items:center; gap:.6rem; color:var(--text-muted); font-size:.92rem; margin-bottom:.6rem; }
    .info-item .fas{ width:18px; color:var(--primary-color); text-align:center; }

    /* history */
    .history-list{ max-height:220px; overflow-y:auto; padding-right:.5rem; }
    .history-item{ padding:.6rem 0; border-bottom:1px solid var(--border-color); }
    .history-date{ font-weight:700; color:var(--primary-color); font-size:.9rem; margin-bottom:.25rem; }
    .history-note{ color:var(--text-muted); font-size:.9rem; display:block }

    /* ajax loader */
    .ajax-loader{
      position:fixed;
      top:0; left:0; right:0; bottom:0;
      background:rgba(11,22,18,0.35);
      display:flex;
      align-items:center;
      justify-content:center;
      z-index:9999;
      opacity:0;
      visibility:hidden;
      transition:opacity .18s ease, visibility .18s ease;
    }
    .ajax-loader.show{ opacity:1; visibility:visible; }
    .ajax-card{ background:#fff; padding:1rem 1.25rem; border-radius:12px; display:flex; gap:.8rem; align-items:center; box-shadow:0 10px 30px rgba(2,6,23,0.12); }
    .spinner{ width:36px; height:36px; border-radius:50%; border:4px solid rgba(0,0,0,0.08); border-top-color:var(--primary-color); animation:spin 1s linear infinite; }
    @keyframes spin{ to { transform:rotate(360deg); } }

    /* summary */
    .summary-panel{ border:1px dashed var(--border-color); padding:1rem; border-radius:10px; background:linear-gradient(180deg,#fff,#fcfcff); }
    .summary-row{ display:flex; justify-content:space-between; font-size:.95rem; margin-bottom:.45rem; color:var(--text-muted); }
    .summary-row strong{ color:var(--text-color); font-weight:700; }

    /* responsive */
    @media (max-width:600px){
      body{ padding:.75rem; font-size:15px; }
      .form-container{ padding:1rem; border-radius:12px; }
      .form-control{ padding:.7rem .9rem; font-size:.94rem; }
      .client-avatar{ width:56px;height:56px;font-size:1.25rem }
    }

    /* micro-animations */
    .fade-in { animation: fadeIn .28s ease both; }
    @keyframes fadeIn { from { opacity:0; transform: translateY(6px);} to { opacity:1; transform: translateY(0);} }

    /* small helper */
    .muted { color: var(--text-muted); font-size: .9rem; }
    
    /* --- Estilos consistentes para Select2 --- */
    .select2-container {
      width: 100% !important;
      font-size: 14px;
    }

    .select2-selection--single {
      height: 40px !important;
      display: flex !important;
      align-items: center !important;
      border: 1.5px solid #28a745 !important;
      border-radius: 8px !important;
      transition: all 0.2s ease-in-out !important;
    }

    .select2-selection__rendered {
      padding-left: 12px !important;
      color: #333 !important;
      line-height: 38px !important;
      white-space: nowrap !important;
      text-overflow: ellipsis !important;
      overflow: hidden !important;
    }

    .select2-selection__arrow {
      height: 38px !important;
      right: 8px !important;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
      border-color: #8A2BE2 !important;
      box-shadow: 0 0 0 2px rgba(138, 43, 226, 0.2) !important;
    }

  </style>
</head>
<body>

  <!-- AJAX Loader overlay -->
  <div id="ajax-loader" class="ajax-loader" aria-hidden="true">
    <div class="ajax-card" role="status" aria-live="polite">
      <div class="spinner" aria-hidden="true"></div>
      <div>
        <div style="font-weight:700; color:var(--primary-color);">Cargando información del cliente</div>
        <div style="font-size:.9rem; color:var(--text-muted)">Un momento por favor…</div>
      </div>
    </div>
  </div>

  <div class="form-container" id="interaction-form-wrapper" tabindex="-1">
    <form id="interaction-form" action="{{ route('interactions.store') }}" method="POST" enctype="multipart/form-data" novalidate>
      @csrf

      <div class="interaction-layout">

        <!-- MAIN COLUMN -->
        <div class="main-column">

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-user-tie"></i> Cliente</h3>
            <div class="form-group">
              <label for="client_id" class="form-label">Buscar y seleccionar cliente <span style="color:var(--danger-color)">*</span></label>
              <select name="client_id" id="client_id" class="form-control" required>
                <option value=""></option>
                @foreach ($clientes as $cliente)
                  <option value="{{ $cliente->cod_ter }}">{{ $cliente->cod_ter }} - {{ $cliente->apl1 }} {{ $cliente->apl2 }} {{ $cliente->nom1 }} {{ $cliente->nom2 }}</option>
                @endforeach
              </select>
              <i class="fas fa-check-circle valid-icon" aria-hidden="true"></i>
              <div class="helper-error" data-for="client_id">Selecciona un cliente.</div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-comments"></i> Detalles de la Interacción</h3>
            <div class="form-group-cluster">
              <div class="form-row" style="display:flex;gap:1rem;flex-wrap:wrap">
                <div class="form-group" style="flex:1; min-width:160px;">
                  <label for="interaction_channel" class="form-label">Canal <span style="color:var(--danger-color)">*</span></label>
                  <select name="interaction_channel" id="interaction_channel" class="form-control" required>
                    <option value="">Selecciona...</option>
                    @foreach($channels as $channel)
                      <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                    @endforeach
                  </select>
                  <i class="fas fa-check-circle valid-icon" aria-hidden="true"></i>
                  <div class="helper-error" data-for="interaction_channel">Selecciona un canal.</div>
                </div>

                <div class="form-group" style="flex:1; min-width:160px;">
                  <label for="interaction_type" class="form-label">Tipo <span style="color:var(--danger-color)">*</span></label>
                  <select name="interaction_type" id="interaction_type" class="form-control" required>
                    <option value="">Selecciona...</option>
                    @foreach($types as $type)
                      <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                  </select>
                  <i class="fas fa-check-circle valid-icon" aria-hidden="true"></i>
                  <div class="helper-error" data-for="interaction_type">Selecciona un tipo.</div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-clipboard-list"></i> Notas de la Interacción</h3>
            <div class="form-group">
              <textarea name="notes" id="notes" class="form-control" placeholder="Añade aquí todos los detalles relevantes..." required></textarea>
              <i class="fas fa-check-circle valid-icon" aria-hidden="true"></i>
              <div class="helper-error" data-for="notes">Escribe las notas de la interacción.</div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-tags"></i> Etiquetas de Interacción</h3>
            <div class="form-group">
              <label for="tags" class="form-label">Categoriza la interacción</label>
              <select name="tags[]" id="tags" class="form-control" multiple>
                <option value="venta">Venta Cruzada</option>
                <option value="soporte">Soporte Técnico</option>
                <option value="queja">Queja o Reclamo</option>
                <option value="cotizacion">Seguimiento Cotización</option>
                <option value="informacion">Solicitud de Información</option>
                <option value="cobranza">Gestión de Cobranza</option>
              </select>
              <div class="helper-error" data-for="tags">Selecciona al menos una etiqueta (si aplica).</div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-check-circle"></i> Resultado de la Gestión</h3>
            <div class="form-group">
              <select name="outcome" id="outcome" class="form-control" required>
                <option value="">Selecciona un resultado...</option>
                @foreach ($outcomes as $outcome)
                  <option value="{{ $outcome->id }}">{{ $outcome->name }}</option>
                @endforeach
              </select>
              <i class="fas fa-check-circle valid-icon" aria-hidden="true"></i>
              <div class="helper-error" data-for="outcome">Selecciona el resultado de la gestión.</div>
            </div>
          </div>

        </div>

        <!-- SIDEBAR -->
        <div class="sidebar-column">

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-user-shield"></i> Agente y Fecha</h3>
            <div class="form-group">
              <div style="background-color:#F7F9FC;padding:0.9rem;border-radius:10px;border:1px solid var(--border-color)">
                <strong>Agente:</strong> {{ auth()->user()->name }} <br>
                <strong>Fecha:</strong> {{ now()->format('d/m/Y h:i A') }}
              </div>
              <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
              <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-address-card"></i> Información del Cliente</h3>
            <div id="client-info-card" class="client-info-card" aria-live="polite">
              <div class="card-header" style="text-align:center;padding-bottom:.75rem;border-bottom:1px solid var(--border-color);margin-bottom:.75rem">
                <div class="client-avatar" id="info-avatar" aria-hidden="true"></div>
                <p id="info-nombre" class="client-name">—</p>
                <span id="info-id" class="client-id-badge" style="display:block;text-align:center;margin-top:.25rem;color:var(--text-muted)"></span>
              </div>
              <div class="card-body">
                <div class="info-item"><i class="fas fa-map"></i><span id="info-distrito">Cargando...</span></div>

                <div class="info-item"><i class="fas fa-tags"></i><span id="info-categoria">Cargando...</span></div>
                <div class="info-item"><i class="fas fa-envelope"></i><span id="info-email">Cargando...</span></div>
                <div class="info-item"><i class="fas fa-phone"></i><span id="info-telefono">Cargando...</span></div>
                <div class="info-item"><i class="fas fa-map-marker-alt"></i><span id="info-direccion">Cargando...</span></div>
                
                <div class="info-item"><i class="fas fa-church"></i><span id="info-cod-congregacion">Cargando...</span></div>
                <div class="info-item"><i class="fas fa-place-of-worship"></i><span id="info-nom-congregacion">Cargando...</span></div>
                <hr style="border-color:var(--border-color);margin:0.75rem 0">
                <div class="info-item"><i class="fas fa-clock"></i><span>Última Inter.: <span id="info-ultima-interaccion">Cargando...</span></span></div>
              </div>
              <div style="text-align:center;margin-top:.75rem">
                <a id="btn-editar-cliente" href="#" class="btn btn-secondary btn-sm">Ver o actualizar ficha completa</a>
              </div>
            </div>
          </div>

          <div class="form-section" id="history-section" style="display:none;">
            <h3 class="form-section-title"><i class="fas fa-history"></i> Historial Reciente</h3>
            <div id="interaction-history-list" class="history-list">
              <!-- items cargados por JS -->
            </div>
          </div>

          <div class="form-section" id="planning-section" style="display:none;">
            <h3 class="form-section-title"><i class="fas fa-calendar-alt"></i> Planificación</h3>
            <div class="form-group-cluster">
              <label class="form-label">Próxima Acción (Fecha y Hora)</label>
              <div style="margin:0.5rem 0 0.75rem; display:flex; gap:.45rem; flex-wrap:wrap;">
                <button type="button" class="btn-quick-date" data-days="1">+1 día</button>
                <button type="button" class="btn-quick-date" data-days="3">+3 días</button>
                <button type="button" class="btn-quick-date" data-days="7">+1 sem</button>
                <button type="button" class="btn-quick-date" data-days="14">+2 sem</button>
              </div>
              <div class="input-with-icon" style="position:relative;">
                <i class="fas fa-calendar-alt" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                <input type="datetime-local" name="next_action_date" class="form-control" style="padding-left:3rem;">
              </div>

              <div class="form-group" style="margin-top:.75rem">
                <label class="form-label">Tipo de acción</label>
                <select name="next_action_type" id="next_action_type" class="form-control">
                  <option value="">Selecciona...</option>
                  @foreach ($nextActions as $action)
                    <option value="{{ $action->id }}">{{ $action->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-paperclip"></i> Adjuntos y Enlaces</h3>
            <div class="form-group-cluster">
              <div class="form-group">
                <label class="form-label">Adjuntar archivos</label>
                <input type="file" name="attachments[]" class="form-control" multiple>
              </div>
              <div class="form-group" style="margin-top:0.75rem">
                <label class="form-label">Enlace de referencia</label>
                <div style="position:relative">
                  <i class="fas fa-link" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                  <input type="url" name="interaction_url" class="form-control" placeholder="https://ejemplo.com" style="padding-left:3rem;">
                </div>
              </div>
            </div>
          </div>

          <!-- RESUMEN DINÁMICO -->
          <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-swatchbook"></i> Resumen rápido</h3>
            <div id="summary-panel" class="summary-panel" aria-live="polite">
              <div class="summary-row"><span>Cliente</span><strong id="summary-client">—</strong></div>
              <div class="summary-row"><span>Resultado</span><strong id="summary-outcome">—</strong></div>
              <div class="summary-row"><span>Próxima acción</span><strong id="summary-nextaction">—</strong></div>
              <div class="summary-row"><span>Etiquetas</span><strong id="summary-tags">—</strong></div>
            </div>
          </div>

        </div>
      </div>

      <div class="form-actions" style="margin-top:1rem; display:flex; justify-content:flex-end; gap:.75rem; border-top:1px solid var(--border-color); padding-top:1rem">
        <a href="{{ route('interactions.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Cancelar</a>
        <button id="clear-draft" type="button" class="btn btn-secondary"><i class="fas fa-trash-alt"></i> Borrar borrador</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Interacción</button>
      </div>
    </form>
  </div>

<!-- SCRIPTS (al final para rendimiento) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

<script>
(function(){
  // ------- Utilities -------
  const $ajaxLoader = $('#ajax-loader');
  const $form = $('#interaction-form');
  const storageKey = 'interaction_form_draft_v1';

  function showLoader(){ $ajaxLoader.addClass('show'); $ajaxLoader.attr('aria-hidden','false'); NProgress.start(); }
  function hideLoader(){ $ajaxLoader.removeClass('show'); $ajaxLoader.attr('aria-hidden','true'); NProgress.done(); }

  // small toast defaults
  toastr.options = {
    "positionClass": "toast-bottom-right",
    "timeOut": "2500",
    "progressBar": true,
  };

  // ------- Select2 init (actualizado para bug visual) -------
  try {
    $('#client_id').select2({
      placeholder: "Escribe para buscar por código o nombre...",
      allowClear: true,
      width: '100%',
      language: { noResults: () => "No se encontraron clientes" },
      dropdownParent: $('body') // 👈 FIX principal
    });

    // mejora visual: sombra y margen en el dropdown
    $('#client_id').on('select2:open', function(){
      const dropdown = $('.select2-container--open .select2-dropdown');
      dropdown.css({
        'border-radius': '8px',
        'margin-top': '2px',
        'box-shadow': '0 8px 16px rgba(0,0,0,0.1)'
      });
    });

  } catch(e){ console.warn('Select2 cliente init failed', e); }

  $('#tags').select2({
    placeholder: "Selecciona una o más etiquetas...",
    width: '100%',
    allowClear: true,
    dropdownParent: $('body')
  });

  // Additional selects
  $('#interaction_channel').select2({ width:'100%', placeholder:'Selecciona...', dropdownParent:$('body') });
  $('#interaction_type').select2({ width:'100%', placeholder:'Selecciona...', dropdownParent:$('body') });
  $('#outcome').select2({ width:'100%', placeholder:'Selecciona un resultado...', dropdownParent:$('body') });
  $('#next_action_type').select2({ width:'100%', placeholder:'Selecciona...', dropdownParent:$('body') });

  // ------- Autosave in localStorage -------
  function saveDraft(){
    try{
      const data = {};
      $form.find('input,textarea,select').each(function(){
        const name = this.name;
        if(!name) return;
        if(this.type === 'file') return;
        if(this.type === 'checkbox' || this.type === 'radio'){
          if(this.checked) data[name] = this.value;
        } else {
          if ($(this).is('select[multiple]')){
            data[name] = $(this).val() || [];
          } else {
            data[name] = $(this).val();
          }
        }
      });
      localStorage.setItem(storageKey, JSON.stringify(data));
      const $wrapper = $('#interaction-form-wrapper');
      $wrapper.css('box-shadow','0 8px 28px rgba(52,91,84,0.06)');
      setTimeout(()=> $wrapper.css('box-shadow','var(--shadow-soft)'), 350);
    }catch(e){
      console.warn('No se pudo guardar el borrador', e);
    }
  }

  function debounce(fn, wait){
    let t;
    return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); };
  }

  const saveDraftDebounced = debounce(saveDraft, 700);

  function loadDraft(){
    try{
      const raw = localStorage.getItem(storageKey);
      if(!raw) return;
      const data = JSON.parse(raw);
      Object.keys(data).forEach(name => {
        const value = data[name];
        const $el = $form.find(`[name="${name}"]`);
        if(!$el.length) return;
        if($el.is('select[multiple]')){
          $el.val(value).trigger('change');
        } else if($el.is('select')){
          $el.val(value).trigger('change');
        } else {
          $el.val(value);
        }
      });
      toastr.info('Borrador restaurado automáticamente');
    }catch(e){
      console.warn('No se pudo cargar el borrador', e);
    }
  }

  $form.on('input change', 'input, textarea, select', function(){
    validateField(this);
    updateSummary();
    saveDraftDebounced();
  });

  loadDraft();
  updateSummary();

  $('#clear-draft').on('click', function(){
    Swal.fire({
      title: '¿Borrar el borrador guardado?',
      text: "Esto eliminará el borrador localmente.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, borrar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        localStorage.removeItem(storageKey);
        $form[0].reset();
        $('#client_id,#tags,#interaction_channel,#interaction_type,#outcome,#next_action_type').val(null).trigger('change');
        $('#planning-section').hide();
        $('#client-info-card').hide();
        $('#history-section').hide();
        updateSummary();
        toastr.success('Borrador eliminado');
      }
    });
  });

  // ------- Validation helpers -------
  function showError($field, message){
    $field.addClass('is-invalid').removeClass('is-valid');
    const name = $field.attr('name');
    $(`[data-for="${name}"]`).text(message).show();
    if($field.hasClass('select2-hidden-accessible')){
      $field.next('.select2-container').find('.select2-selection--single, .select2-selection--multiple').addClass('is-invalid').removeClass('is-valid');
    }
  }
  function showSuccess($field){
    $field.addClass('is-valid').removeClass('is-invalid');
    const name = $field.attr('name');
    $(`[data-for="${name}"]`).hide();
    if($field.hasClass('select2-hidden-accessible')){
      $field.next('.select2-container').find('.select2-selection--single, .select2-selection--multiple').addClass('is-valid').removeClass('is-invalid');
    }
  }
  function clearValidation($field){
    $field.removeClass('is-valid is-invalid');
    const name = $field.attr('name');
    $(`[data-for="${name}"]`).hide();
    if($field.hasClass('select2-hidden-accessible')){
      $field.next('.select2-container').find('.select2-selection--single, .select2-selection--multiple').removeClass('is-valid is-invalid');
    }
  }

  function validateField(el){
    const $el = $(el);
    const name = $el.attr('name');
    if(!$el.prop('required')) {
      if($el.val() && $el.val().toString().trim() !== ''){
        showSuccess($el);
      } else {
        clearValidation($el);
      }
      return true;
    }

    const val = $el.val();
    if($el.is('select[multiple]')){
      if(Array.isArray(val) && val.length>0){
        showSuccess($el);
        return true;
      } else {
        showError($el, 'Selecciona al menos una opción.');
        return false;
      }
    }

    if(!val || val.toString().trim() === ''){
      showError($el, 'Este campo es obligatorio.');
      return false;
    }
    if($el.attr('type') === 'url' && val){
      try{
        new URL(val);
      }catch(e){
        showError($el, 'Ingresa una URL válida.');
        return false;
      }
    }
    showSuccess($el);
    return true;
  }

  $form.on('submit', function(e){
    e.preventDefault();
    let valid = true;
    $form.find('select[required], textarea[required], input[required]').each(function(){
      const ok = validateField(this);
      if(!ok) valid = false;
    });
    if(!valid){
      const $first = $form.find('.is-invalid').first();
      $('html,body').animate({scrollTop: $first.offset().top - 90}, 350);
      Swal.fire({
        icon: 'error',
        title: 'Errores en el formulario',
        text: 'Por favor corrige los campos marcados antes de enviar.',
      });
      return false;
    }

    Swal.fire({
      title: 'Confirmar envío',
      text: "¿Deseas enviar la interacción ahora?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Enviar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if(result.isConfirmed){
        localStorage.removeItem(storageKey);
        showLoader();
        $form.off('submit');
        $form.submit();
      }
    });

  });

  // ------- AJAX: load client data -------
  $('#client_id').on('change', function(){
    const cod_ter = $(this).val();
    const clientCard = $('#client-info-card');
    const historySection = $('#history-section');
    const historyList = $('#interaction-history-list');

    if(!cod_ter){
      clientCard.fadeOut();
      historySection.fadeOut();
      $('#summary-client').text('—');
      return;
    }

    showLoader();
    $.ajax({
      url: `/interactions/cliente/${cod_ter}`,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
    }).done(function(data){
      const initials = ((data.nom1||'').charAt(0) + (data.apl1||'').charAt(0)).toUpperCase();
      $('#info-avatar').text(initials || '—');
      $('#info-nombre').text(data.nom_ter ?? 'No registrado');
      $('#info-id').text(`ID: ${data.cod_ter ?? 'N/A'}`);
      
      $('#info-distrito').text(data.distrito.NOM_DIST ?? 'No registrado');

      $('#info-categoria').text(data.maeTipos.nombre ?? 'No registrado');
      $('#info-email').text(data.email ?? 'No registrado');
      $('#info-telefono').text(data.tel1 ?? 'No registrado');
      $('#info-direccion').text(data.dir ?? 'No registrado');

      $('#info-cod-congregacion').text(data.congrega ? data.congrega : 'No registrado'); //CODIGO
      $('#info-nom-congregacion').text(data.congregaciones?.nombre ?? 'No registrado'); //NOMBRE

            
      $('#info-ultima-interaccion').text(data.last_interaction_date ?? 'Ninguna');
      $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`);

      historyList.empty();
      if(data.history && data.history.length){
        data.history.forEach(item => {
          const html = `<div class="history-item"><div class="history-date">${item.date} - ${item.agent}</div><div class="history-note">${item.notes}</div></div>`;
          historyList.append(html);
        });
        historySection.fadeIn();
      } else {
        historySection.fadeOut();
      }

      clientCard.fadeIn();
      $('#summary-client').text($('#client_id option:selected').text() || (data.nom_ter ?? '—'));
      toastr.success('Información del cliente cargada');
    }).fail(function(){
      Swal.fire({
        icon: 'error',
        title: 'No se pudo cargar el cliente',
        text: 'Revisa la conexión o inténtalo más tarde.'
      });
      $('#client-info-card').fadeOut();
      $('#history-section').fadeOut();
    }).always(function(){
      hideLoader();
    });
  });

  // ------- Outcome -> planning logic -------
  const outcomeSelect = $('#outcome');
  const planningSection = $('#planning-section');
  function handleOutcomeChange(){
    const selectedOutcomeText = outcomeSelect.find("option:selected").text().trim();
    if(selectedOutcomeText === 'Pendiente' || selectedOutcomeText === 'No contesta' || selectedOutcomeText.toLowerCase().includes('pendiente')){
      planningSection.slideDown();
    } else {
      planningSection.slideUp();
    }
    outcomeSelect.removeClass('outcome-concretada outcome-pendiente outcome-nocontesta');
    if(selectedOutcomeText === 'Concretada'){
      outcomeSelect.addClass('outcome-concretada');
    } else if(selectedOutcomeText === 'Pendiente'){
      outcomeSelect.addClass('outcome-pendiente');
    } else if(selectedOutcomeText === 'No contesta'){
      outcomeSelect.addClass('outcome-nocontesta');
    }
    updateSummary();
  }
  outcomeSelect.on('change', handleOutcomeChange);
  handleOutcomeChange();

  // ------- Quick date buttons -------
  $('.btn-quick-date').on('click', function(){
    const daysToAdd = parseInt($(this).data('days')) || 0;
    const nextActionInput = $('input[name="next_action_date"]');
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + daysToAdd);
    const year = targetDate.getFullYear();
    const month = String(targetDate.getMonth()+1).padStart(2,'0');
    const day = String(targetDate.getDate()).padStart(2,'0');
    const hours = '09';
    const minutes = '00';
    const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
    nextActionInput.val(formatted).addClass('is-valid');
    updateSummary();
    saveDraftDebounced();
    toastr.info('Fecha establecida: ' + new Date(formatted).toLocaleString());
  });

  // ------- Dynamic summary -------
  function updateSummary(){
    const clientText = $('#client_id option:selected').text() || $('#info-nombre').text() || '—';
    const outcomeText = $('#outcome option:selected').text() || '—';
    const tags = $('#tags').val();
    const tagsText = (tags && tags.length) ? tags.join(', ') : '—';
    const nextAction = $('input[name="next_action_date"]').val();
    const nextActionText = nextAction ? new Date(nextAction).toLocaleString() : '—';

    $('#summary-client').text(clientText);
    $('#summary-outcome').text(outcomeText);
    $('#summary-tags').text(tagsText);
    $('#summary-nextaction').text(nextActionText);
  }

  updateSummary();

  $(document).on('focus', '.select2-selection', function(){
    $(this).css('box-shadow','0 6px 20px var(--focus-ring)');
  }).on('blur', '.select2-selection', function(){
    $(this).css('box-shadow','');
  });

  setTimeout(function(){
    $form.find('select[required], textarea[required], input[required]').each(function(){ validateField(this); });
  }, 400);

  $(document).on('keydown', function(e){
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's'){
      e.preventDefault();
      saveDraft();
      toastr.success('Borrador guardado (Ctrl+S)');
    }
    if(e.key === 'Escape'){
      if($ajaxLoader.hasClass('show')) hideLoader();
    }
  });

  $(document).on('input focus', 'input,textarea,select', function(){
    const name = $(this).attr('name');
    $(`[data-for="${name}"]`).fadeOut(120);
  });

})();
</script>

</body>
</html>
