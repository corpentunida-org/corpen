<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Interacciones - Corregido y Mejorado</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
    
    :root {
      --primary-color: #345B54;
      --primary-hover: #2A4842;
      --success-color: #28a745; /* Color para validación */
      --text-color: #2D3748;
      --text-muted: #718096;
      --background-color: #F8F7F5;
      --card-bg: #FFFFFF;
      --border-color: #E2E8F0;
      --focus-ring: rgba(52, 91, 84, 0.25);
      --cluster-bg: #FDFCFB;
      --danger-color: #dc3545; /* Añadido para el asterisco */
    }

    /* === BASE Y TIPOGRAFÍA === */
    *, *::before, *::after { box-sizing: border-box; }
    body {
      background-color: var(--background-color);
      font-family: 'Inter', sans-serif;
      font-size: 16px;
      line-height: 1.6;
      margin: 0;
      color: var(--text-color);
    }
    
    /* === CONTENEDOR PRINCIPAL === */
    .form-container {
      max-width: 1280px;
      margin: 2.5rem auto;
      background-color: var(--card-bg);
      padding: 2.5rem 3rem;
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.02), 0 15px 35px rgba(45, 55, 72, 0.07);
      border: 1px solid var(--border-color);
    }

    /* === LAYOUT === */
    .interaction-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; }
    
    /* === FORMULARIO === */
    .form-section { margin-bottom: 2.5rem; }
    .form-section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; letter-spacing: -0.01em; }
    .form-section-title .fas { color: var(--primary-color); }
    .form-group { margin-bottom: 1rem; position: relative; } /* Position relative para el icono */
    .form-label { display: block; font-weight: 500; font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-muted); }

    .form-control, .form-control-file, .select2-container .select2-selection--single {
      width: 100%;
      padding: 0.8rem 1rem;
      font-size: 0.95rem;
      border: 1px solid var(--border-color);
      border-radius: 10px;
      transition: all 0.25s ease-in-out;
      background-color: #fff;
    }
    
    .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px var(--focus-ring); outline: none; }

    /* [MEJORA] Estilo para campos validados */
    .form-control.is-valid {
        border-color: var(--success-color);
    }
    .form-control.is-valid:focus {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    }
    .valid-icon {
        position: absolute;
        right: 1rem;
        top: calc(50% + 5px); /* Ajuste para alinear con el input */
        color: var(--success-color);
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }
    .is-valid + .valid-icon {
        opacity: 1;
    }

    /* === GRUPOS Y OTROS === */
    .form-group-cluster { background-color: var(--cluster-bg); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 16px; margin-top: 1rem; }
    .input-with-icon { position: relative; }
    .input-with-icon .fas { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
    .input-with-icon .form-control { padding-left: 2.75rem; }
    textarea.form-control { min-height: 140px; resize: vertical; }
    
    /* === BOTONES === */
    .form-actions { margin-top: 1rem; padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem; }
    .btn { display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.8rem 1.6rem; font-size: 0.9rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: all 0.25s ease; text-decoration: none; border: 1px solid transparent; }
    .btn-primary { background-color: var(--primary-color); color: #fff; }
    .btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-2px); }
    .btn-secondary { background-color: var(--card-bg); color: var(--text-muted); border: 1px solid var(--border-color); }
    .btn-secondary:hover { background-color: #F7F9FC; }

    /* === SIDEBAR === */
    .client-info-card { 
      display: none; 
      padding: 1.5rem; 
      background: linear-gradient(180deg, #FDFDFD 0%, #F8F9FA 100%); 
      border: 1px solid var(--border-color); 
      border-radius: 16px; 
    }
    .client-info-card .card-header { 
      text-align: center; 
      margin-bottom: 1.5rem; 
      padding-bottom: 1.5rem; 
      border-bottom: 1px solid var(--border-color); 
    }
    .client-info-card .client-name { 
      font-size: 1.25rem; 
      font-weight: 600; 
      color: var(--primary-color); 
    }
    .client-info-card .btn-edit-wrapper { 
      margin-top: 1.5rem; 
      text-align: center; 
    }

    /* NUEVOS ESTILOS PARA LA TARJETA ENRIQUECIDA */
    .client-avatar {
        width: 70px; height: 70px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 600;
        margin: 0 auto 1rem;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    .client-id-badge {
        display: inline-block;
        background: #eef2f6;
        color: var(--text-muted);
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 20px;
        margin-top: 0.25rem;
    }
    .client-info-card .card-body { padding-top: 1.5rem; }
    .info-item { display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1rem; color: var(--text-muted); font-size: 0.9rem; }
    .info-item .fas { color: var(--primary-color); width: 16px; text-align: center; }
    .btn-sm { padding: 0.5rem 1rem; font-size: 0.8rem; }

    /* ESTILOS PARA BOTONES DE FECHA RÁPIDA */
    .btn-quick-date {
        background: #F0F2F5;
        border: 1px solid var(--border-color);
        padding: 0.3rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-quick-date:hover {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* ESTILOS PARA INDICADORES DE ESTADO VISUAL EN SELECTS */
    .form-control.outcome-concretada { border-left: 4px solid #28a745; }
    .form-control.outcome-pendiente { border-left: 4px solid #ffc107; }
    .form-control.outcome-nocontesta { border-left: 4px solid #dc3545; }

    /* ESTILOS PARA HISTORIAL RÁPIDO */
    .history-list { max-height: 200px; overflow-y: auto; }
    .history-item { border-bottom: 1px solid var(--border-color); padding: 0.8rem 0; font-size: 0.85rem; }
    .history-item:last-child { border-bottom: none; }
    .history-date { font-weight: 600; color: var(--primary-color); }
    .history-note { color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }


    /* === RESPONSIVE === */
    @media (max-width: 1024px) { .interaction-layout { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<div class="form-container">
  <form action="{{ route('interactions.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="interaction-layout">
      
      <div class="main-column">

        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-user-tie"></i>Cliente</h3>
          <div class="form-group">
            <label for="client_id" class="form-label">Buscar y seleccionar cliente <span style="color:var(--danger-color)">*</span></label>
            <select name="client_id" id="client_id" class="form-control" required>
              <option value=""></option>
              @foreach ($clientes as $cliente)
                <option value="{{ $cliente->cod_ter }}">{{ $cliente->cod_ter }} - {{ $cliente->apl1 }} {{ $cliente->nom1 }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-comments"></i>Detalles de la Interacción</h3>
            <div class="form-group-cluster">
                <div class="form-row" style="display:flex; gap:1rem;">
                    <div class="form-group" style="flex:1;">
                        <label for="interaction_channel" class="form-label">Canal</label>
                        <select name="interaction_channel" id="interaction_channel" class="form-control" required>
                            <option value="">Selecciona...</option>
                            @foreach($channels as $channel)
                            <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                            @endforeach
                        </select>
                         <i class="fas fa-check-circle valid-icon"></i>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Tipo</label>
                        <select name="interaction_type" class="form-control" required>
                            <option value="">Selecciona...</option>
                            <option>Entrante</option>
                            <option>Saliente</option>
                            <option>Seguimiento</option>
                        </select>
                         <i class="fas fa-check-circle valid-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-clipboard-list"></i>Notas de la Interacción</h3>
          <div class="form-group">
            <textarea name="notes" class="form-control" placeholder="Añade aquí todos los detalles relevantes..." required></textarea>
            <i class="fas fa-check-circle valid-icon"></i>
          </div>
        </div>
        
        <!-- Campo de Etiquetas (Tags) -->
        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-tags"></i>Etiquetas de Interacción</h3>
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
            </div>
        </div>

        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-check-circle"></i>Resultado de la Gestión</h3>
           <div class="form-group">
              <select name="outcome" class="form-control" required>
                <option value="">Selecciona un resultado...</option>
                <option>Concretada</option>
                <option>No contesta</option>
                <option>Pendiente</option>
              </select>
              <i class="fas fa-check-circle valid-icon"></i>
            </div>
        </div>

      </div>

      <div class="sidebar-column">

        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-user-shield"></i>Agente y Fecha</h3>
          <div class="form-group">
            <div style="background-color: #F7F9FC; padding: 0.8rem 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                <strong>Agente:</strong> {{ auth()->user()->name }} <br>
                <strong>Fecha:</strong> {{ now()->format('d/m/Y h:i A') }}
            </div>
            <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
            <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
          </div>
        </div>

        <div class="form-section">
          <h3 class="form-section-title"><i class="fas fa-address-card"></i>Información del Cliente</h3>
          <!-- Tarjeta de Cliente Enriquecida -->
          <div id="client-info-card" class="client-info-card">
              <div class="card-header">
                  <div class="client-avatar" id="info-avatar"></div>
                  <p id="info-nombre" class="client-name"></p>
                  <span id="info-id" class="client-id-badge"></span>
              </div>
              <div class="card-body">
                  <div class="info-item">
                      <i class="fas fa-envelope"></i>
                      <span id="info-email">Cargando...</span>
                  </div>
                  <div class="info-item">
                      <i class="fas fa-phone"></i>
                      <span id="info-telefono">Cargando...</span>
                  </div>
                  <div class="info-item">
                      <i class="fas fa-clock"></i>
                      <span>Última Inter.: <span id="info-ultima-interaccion">Cargando...</span></span>
                  </div>
              </div>
              <div class="btn-edit-wrapper">
                  <a id="btn-editar-cliente" href="#" class="btn btn-secondary btn-sm">Ver Ficha Completa</a>
              </div>
          </div>
        </div>
        
        <!-- Historial Rápido de Interacciones -->
        <div class="form-section" id="history-section" style="display:none;">
            <h3 class="form-section-title"><i class="fas fa-history"></i>Historial Reciente</h3>
            <div id="interaction-history-list" class="history-list">
                <!-- Los items del historial se cargarán aquí vía JS -->
            </div>
        </div>

        <div class="form-section" id="planning-section">
          <h3 class="form-section-title"><i class="fas fa-calendar-alt"></i>Planificación</h3>
           <div class="form-group-cluster">
                <div class="form-group">
                    <label class="form-label">Próxima Acción (Fecha y Hora)</label>
                    <div class="quick-date-actions" style="margin-top: 0.75rem; display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <button type="button" class="btn-quick-date" data-days="1">+1 día</button>
                        <button type="button" class="btn-quick-date" data-days="3">+3 días</button>
                        <button type="button" class="btn-quick-date" data-days="7">+1 sem</button>
                        <button type="button" class="btn-quick-date" data-days="14">+2 sem</button>
                    </div>
                    <div class="input-with-icon">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="datetime-local" name="next_action_date" class="form-control">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem; margin-bottom: 0;">
                    <label class="form-label">Tipo de acción</label>
                    <select name="next_action_type" class="form-control">
                        <option value="">Selecciona...</option>
                        <option>Volver a llamar</option>
                        <option>Enviar WhatsApp</option>
                        <option>Reunión</option>
                        <option>Envío de email</option>
                        <option>Cita presencial</option>
                    </select>
                </div>
           </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title"><i class="fas fa-paperclip"></i>Adjuntos y Enlaces</h3>
             <div class="form-group-cluster">
                 <div class="form-group">
                    <label class="form-label">Adjuntar archivos</label>
                    <input type="file" name="attachments[]" class="form-control" multiple>
                </div>
                <div class="form-group" style="margin-top: 1rem; margin-bottom:0;">
                    <label class="form-label">Enlace de referencia</label>
                    <div class="input-with-icon">
                        <i class="fas fa-link"></i>
                        <input type="url" name="interaction_url" class="form-control" placeholder="https://ejemplo.com">
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('interactions.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Cancelar</a>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Interacción</button>
    </div>

  </form>
</div>

<script>
$(document).ready(function () {
  // Inicialización de Select2 para el cliente
  $('#client_id').select2({
    placeholder: "Escribe para buscar por código o nombre...",
    allowClear: true,
    width: '100%',
    language: { noResults: () => "No se encontraron clientes" }
  });

  // Inicialización de Select2 para Etiquetas
  $('#tags').select2({
      placeholder: "Selecciona una o más etiquetas...",
      width: '100%',
      allowClear: true,
  });

  // Lógica Condicional para la "Próxima Acción"
  const outcomeSelect = $('select[name="outcome"]');
  const planningSection = $('#planning-section');

  // Ocultar al inicio
  planningSection.hide();

  outcomeSelect.on('change', function() {
      const selectedOutcome = $(this).val();
      if (selectedOutcome === 'Pendiente' || selectedOutcome === 'No contesta') {
          planningSection.slideDown(); // Una animación suave
      } else {
          planningSection.slideUp();
      }

      // Indicadores de Estado Visual en Selects
      $(this).removeClass('outcome-concretada outcome-pendiente outcome-nocontesta');
      if (selectedOutcome === 'Concretada') {
          $(this).addClass('outcome-concretada');
      } else if (selectedOutcome === 'Pendiente') {
          $(this).addClass('outcome-pendiente');
      } else if (selectedOutcome === 'No contesta') {
          $(this).addClass('outcome-nocontesta');
      }
  }).trigger('change'); // Trigger al cargar para aplicar estilos iniciales


  // Carga de datos del cliente
  $('#client_id').on('change', function () {
    let cod_ter = $(this).val();
    const clientCard = $('#client-info-card');
    const historySection = $('#history-section');
    const historyList = $('#interaction-history-list');

    if (cod_ter) {
      $.ajax({
        url: `/interactions/cliente/${cod_ter}`, // Asume que este endpoint devuelve 'email', 'telefono', 'last_interaction_date', y 'history'
        type: 'GET',
        success: function (data) {
          // Avatar con iniciales
          const initials = (data.nom1 ? data.nom1[0] : '') + (data.apl1 ? data.apl1[0] : '');
          $('#info-avatar').text(initials.toUpperCase());
          
          $('#info-nombre').text(`${data.nom1 ?? ''} ${data.apl1 ?? ''}`);
          $('#info-id').text(`ID: ${data.cod_ter ?? 'N/A'}`);
          $('#info-email').text(data.email ?? 'No registrado');
          $('#info-telefono').text(data.telefono ?? 'No registrado');
          $('#info-ultima-interaccion').text(data.last_interaction_date ?? 'Ninguna'); // Necesitarías este dato desde el backend

          $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`); // Ruta de ejemplo
          clientCard.fadeIn();

          // Llenar historial reciente
          historyList.empty(); // Limpiar la lista anterior
          if (data.history && data.history.length > 0) {
              data.history.forEach(item => {
                  const historyHtml = `
                      <div class="history-item">
                          <div class="history-date">${item.date} - ${item.agent}</div>
                          <p class="history-note">${item.notes}</p>
                      </div>
                  `;
                  historyList.append(historyHtml);
              });
              historySection.fadeIn();
          } else {
              historySection.fadeOut();
          }

        },
        error: function () { 
          clientCard.fadeOut(); 
          historySection.fadeOut();
        }
      });
    } else {
      clientCard.fadeOut();
      historySection.fadeOut();
    }
  });

  // [MEJORA] Lógica para el indicador visual de campos llenos
  const fieldsToValidate = 'select[required], textarea[required], input[required]';

  $(fieldsToValidate).on('change keyup', function() {
    const input = $(this);
    if (input.val() && input.val().trim() !== '') {
      input.addClass('is-valid');
    } else {
      input.removeClass('is-valid');
    }
  });

  // Manejo especial para Select2, que oculta el select original
  $('#client_id, #interaction_channel, select[name="interaction_type"], select[name="outcome"], #tags').on('change', function() {
    const select = $(this);
    if (select.val() && select.val().length > 0) { // Check para selects múltiples
        select.next('.select2-container').find('.select2-selection--single, .select2-selection--multiple').addClass('is-valid');
    } else {
        select.next('.select2-container').find('.select2-selection--single, .select2-selection--multiple').removeClass('is-valid');
    }
  });


  // Sugerencias Rápidas para la "Próxima Acción"
  $('.btn-quick-date').on('click', function() {
      const daysToAdd = parseInt($(this).data('days'));
      const nextActionInput = $('input[name="next_action_date"]');
      
      let targetDate = new Date();
      targetDate.setDate(targetDate.getDate() + daysToAdd);
      
      // Formatear a 'YYYY-MM-DDTHH:mm' que es lo que espera datetime-local
      const year = targetDate.getFullYear();
      const month = String(targetDate.getMonth() + 1).padStart(2, '0');
      const day = String(targetDate.getDate()).padStart(2, '0');
      const hours = '09'; // Poner una hora por defecto, ej: 9 AM
      const minutes = '00';

      const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;
      nextActionInput.val(formattedDate).addClass('is-valid');
  });

});
</script>

</body>
</html>