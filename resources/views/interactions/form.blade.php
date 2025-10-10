<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión de Interacciones</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Select2 -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
    :root {
      --primary-color: #0056b3;
      --secondary-color: #6c757d;
      --danger-color: #dc3545;
      --text-color: #343a40;
      --muted-text: #6f7478;
      --border-light: #e9ecef;
      --border-dark: #ced4da;
      --background: #f5f7fa;
      --card-bg: #ffffff;
      --focus-ring: rgba(0, 86, 179, 0.25);
    }

    body {
      background: var(--background);
      font-family: 'Segoe UI', Roboto, Arial, sans-serif;
      font-size: 0.7rem;
      margin: 0;
      color: var(--text-color);
    }

    .corporate-form-container {
      max-width: 700px;
      margin: 1rem auto;
      background: var(--card-bg);
      padding: 0.8rem;
      border-radius: 6px;
      border: 1px solid var(--border-light);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .form-section-title {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--primary-color);
      margin: 0.8rem 0 0.4rem;
      padding-bottom: 0.25rem;
      border-bottom: 1px solid var(--primary-color);
      display: flex;
      align-items: center;
    }

    .section-icon { margin-right: 0.4rem; font-size: 0.9rem; }

    .form-group { margin-bottom: 0.4rem; }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 0.4rem;
    }

    .form-row .form-group { flex: 1; min-width: 150px; }

    .form-label {
      display: block;
      font-weight: 600;
      font-size: 0.7rem;
      margin-bottom: 0.2rem;
    }

    .required-asterisk { color: var(--danger-color); }

    .form-control,
    .form-control-file {
      width: 100%;
      padding: 0.3rem 0.5rem;
      font-size: 0.7rem;
      border: 1px solid var(--border-dark);
      border-radius: 4px;
      height: 1.6rem;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.1rem var(--focus-ring);
      outline: none;
    }

    textarea.form-control {
      min-height: 60px;
      height: auto;
      resize: vertical;
    }

    .form-control-static {
      padding: 0.3rem 0.5rem;
      font-size: 0.7rem;
      background-color: var(--background);
      border: 1px solid var(--border-light);
      border-radius: 4px;
      display: flex;
      align-items: center;
      height: 1.6rem;
    }

    .static-icon {
      margin-right: 0.4rem;
      color: var(--secondary-color);
      font-size: 0.8rem;
    }

    /* Select2 */
    .select2-container--default .select2-selection--single {
      height: 1.6rem;
      border: 1px solid var(--border-dark);
      border-radius: 4px;
      display: flex;
      align-items: center;
      font-size: 0.7rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      padding-left: 0.5rem;
      font-size: 0.7rem;
      line-height: 1.6rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 1.6rem;
      right: 4px;
    }

    .form-actions {
      margin-top: 1rem;
      padding-top: 0.4rem;
      border-top: 1px solid var(--border-light);
      display: flex;
      justify-content: flex-end;
      gap: 0.4rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.6rem;
      font-size: 0.7rem;
      font-weight: 600;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      line-height: 1;
    }

    .btn .fas { margin-right: 0.3rem; }

    .btn-primary {
      background-color: var(--primary-color);
      color: #fff;
      border: 1px solid var(--primary-color);
    }

    .btn-primary:hover { background-color: #004494; }

    .btn-secondary {
      background-color: #fff;
      color: var(--secondary-color);
      border: 1px solid var(--border-dark);
    }

    .btn-secondary:hover {
      background-color: var(--background);
      color: var(--text-color);
    }

    /* TARJETA CLIENTE */
    #client-info {
      display: none;
      padding: 0.5rem;
      margin-top: 0.5rem;
      background-color: var(--background);
      border: 1px solid var(--border-light);
      border-radius: 4px;
      font-size: 0.7rem;
    }

    #client-info p { margin: 0.15rem 0; }

    #client-info i { color: var(--primary-color); margin-right: 0.3rem; }

    @media (max-width: 600px) { .form-row { flex-direction: column; } }
  </style>
</head>
<body>

<form action="{{ route('interactions.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="corporate-form-container">

    <!-- CLIENTE -->
    <h3 class="form-section-title"><i class="fas fa-user-tie section-icon"></i> Cliente</h3>
    <div class="form-group">
      <label for="client_id" class="form-label">Cliente <span class="required-asterisk">*</span></label>
      <select name="client_id" id="client_id" class="form-control" required>
        <option value="">Selecciona...</option>
        @foreach ($clientes as $cliente)
          <option value="{{ $cliente->cod_ter }}">{{ $cliente->cod_ter }} - {{ $cliente->apl1 }} {{ $cliente->nom1 }}</option>
        @endforeach
      </select>

      {{-- Tarjeta informativa --}}
      <div id="client-info">
        <p><i class="fas fa-id-card"></i> <strong>NIT / Cédula:</strong> <span id="info-cod"></span></p>
        <p><i class="fas fa-user"></i> <strong>Nombre:</strong> <span id="info-nombre"></span></p>
        <p><i class="fas fa-user-tag"></i> <strong>Tipo de Cliente:</strong> <span id="info-tipo"></span></p>
        <p><i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> <span id="info-dir"></span></p>
        <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> <span id="info-tel"></span></p>
        <p><i class="fas fa-mobile-alt"></i> <strong>Celular:</strong> <span id="info-cel"></span></p>
        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <span id="info-email"></span></p>
        <p><i class="fas fa-city"></i> <strong>Ciudad:</strong> <span id="info-ciudad"></span></p>
        <p><i class="fas fa-map"></i> <strong>Departamento:</strong> <span id="info-departamento"></span></p>
        <p><i class="fas fa-globe-americas"></i> <strong>País:</strong> <span id="info-pais"></span></p>
        <p><i class="fas fa-landmark"></i> <strong>Distrito:</strong> <span id="info-distrito"></span></p>
        <p><i class="fas fa-church"></i> <strong>Congregación:</strong> <span id="info-congregacion"></span></p>
        <p><i class="fas fa-hashtag"></i> <strong>Código Congregación:</strong> <span id="info-codcong"></span></p>

        <div style="margin-top: 0.5rem; text-align: right;">
          <a id="btn-editar-cliente" href="#" class="btn btn-primary" target="_blank">
            <i class="fas fa-edit"></i> Editar Cliente
          </a>
        </div>
      </div>
    </div>

    <!-- AGENTE Y FECHA -->
    <h3 class="form-section-title"><i class="fas fa-headset section-icon"></i> Agente y Fecha</h3>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Agente</label>
        <div class="form-control-static"><i class="fas fa-user-tag static-icon"></i> {{ auth()->user()->name }}</div>
        <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
      </div>
      <div class="form-group">
        <label class="form-label">Fecha y Hora</label>
        @php $now = now(); @endphp
        <div class="form-control-static"><i class="fas fa-clock static-icon"></i> {{ $now->format('d/m/Y h:i A') }}</div>
        <input type="hidden" name="interaction_date" value="{{ $now->toDateTimeString() }}">
      </div>
    </div>

    <!-- INTERACCIÓN -->
    <h3 class="form-section-title"><i class="fas fa-comments section-icon"></i> Interacción</h3>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Canal</label>
        <select name="interaction_channel" class="form-control" required>
          <option value="">Selecciona...</option>
          <option>Teléfono</option>
          <option>WhatsApp</option>
          <option>Email</option>
          <option>Presencial</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Tipo</label>
        <select name="interaction_type" class="form-control" required>
          <option value="">Selecciona...</option>
          <option>Entrante</option>
          <option>Saliente</option>
          <option>Seguimiento</option>
        </select>
      </div>
    </div>

    <!-- NOTAS -->
    <h3 class="form-section-title"><i class="fas fa-clipboard-list section-icon"></i> Notas</h3>
    <div class="form-group">
      <textarea name="notes" class="form-control" placeholder="Detalles..." required></textarea>
    </div>

    <!-- RESULTADO -->
    <h3 class="form-section-title"><i class="fas fa-check-circle section-icon"></i> Resultado</h3>
    <div class="form-group">
      <select name="outcome" class="form-control" required>
        <option value="">Selecciona...</option>
        <option>Concretada</option>
        <option>No contesta</option>
        <option>Pendiente</option>
      </select>
    </div>

    <!-- PRÓXIMA ACCIÓN -->
    <h3 class="form-section-title"><i class="fas fa-calendar-alt section-icon"></i> Próxima Acción</h3>
    <div class="form-row">
      <div class="form-group">
        <input type="datetime-local" name="next_action_date" class="form-control">
      </div>
      <div class="form-group">
        <select name="next_action_type" class="form-control">
          <option value="">Selecciona...</option>
          <option>Volver a llamar</option>
          <option>Enviar WhatsApp</option>
          <option>Reunión</option>
        </select>
      </div>
    </div>

    <!-- ADJUNTOS -->
    <h3 class="form-section-title"><i class="fas fa-paperclip section-icon"></i> Adjuntos</h3>
    <div class="form-group">
      <input type="file" name="attachments[]" class="form-control-file" multiple>
      <small style="color: var(--muted-text); font-size: 0.65rem;">PDF, JPG, PNG, DOCX, XLSX</small>
    </div>

    <!-- ENLACE -->
    <h3 class="form-section-title"><i class="fas fa-link section-icon"></i> Enlace</h3>
    <div class="form-group">
      <input type="url" name="interaction_url" class="form-control" placeholder="https://...">
    </div>

    <!-- BOTONES -->
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
      <a href="{{ route('interactions.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Cancelar</a>
    </div>

  </div>
</form>

<script>
$(document).ready(function () {
  $('#client_id').select2({
    placeholder: "Busca cliente...",
    allowClear: true,
    width: '100%',
    language: { noResults: () => "Sin resultados" }
  });

  $('#client_id').on('change', function () {
    let cod_ter = $(this).val();
    if (cod_ter) {
      $.ajax({
        url: `/interactions/cliente/${cod_ter}`,
        type: 'GET',
        success: function (data) {
          $('#info-cod').text(data.cod_ter ?? 'N/A');
          $('#info-nombre').text(data.nom_ter ?? 'N/A');
          $('#info-tipo').text(data.tipo_cliente ?? 'N/A');
          $('#info-dir').text(data.dir ?? 'N/A');
          $('#info-tel').text(data.tel1 ?? 'N/A');
          $('#info-cel').text(data.cel ?? 'N/A');
          $('#info-email').text(data.email ?? 'N/A');
          $('#info-ciudad').text(data.ciudad ?? 'N/A');
          $('#info-departamento').text(data.departamento ?? 'N/A');
          $('#info-pais').text(data.pais ?? 'N/A');
          $('#info-distrito').text(data.distrito ?? 'N/A');
          $('#info-congregacion').text(data.congregacion ?? 'N/A');
          $('#info-codcong').text(data.cod_cong ?? 'N/A');

          $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`);
          $('#client-info').fadeIn();
        },
        error: function () {
          $('#client-info').fadeOut();
        }
      });
    } else {
      $('#client-info').fadeOut();
    }
  });
});
</script>

</body>
</html>
