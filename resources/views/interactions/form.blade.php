<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Interacciones</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />

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
            --light-text-color: #6f7478;
            --border-color-light: #e9ecef;
            --border-color-dark: #ced4da;
            --background-light: #f8f9fa;
            --input-bg: #ffffff;
            --focus-ring: rgba(0, 86, 179, 0.25);
        }

        body {
            background-color: var(--background-light);
            margin: 0;
            padding: 0;
        }

        .corporate-form-container {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 0.95rem;
            color: var(--text-color);
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-width: 950px;
            margin: 2rem auto;
            border: 1px solid var(--border-color-light);
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 2.5rem 0 1.2rem;
            padding-bottom: .8rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
        }

        .form-section-title .section-icon { margin-right: 0.8rem; color: var(--primary-color); font-size: 1.4rem; }
        .form-group { margin-bottom: 1.5rem; }

        .form-row { display: flex; flex-wrap: wrap; margin-left: -0.75rem; margin-right: -0.75rem; }
        .form-row .form-group { padding-left: 0.75rem; padding-right: 0.75rem; flex: 1; min-width: 250px; }

        .form-label { display: block; margin-bottom: 0.6rem; font-weight: 600; color: var(--text-color); font-size: 0.9rem; }
        .required-asterisk { color: var(--danger-color); margin-left: 0.25rem; }

        .form-control,
        .form-control-file {
            display: block;
            width: 100%;
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
            color: var(--text-color);
            background-color: var(--input-bg);
            border: 1px solid var(--border-color-dark);
            border-radius: 6px;
            transition: 0.15s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem var(--focus-ring);
            outline: none;
        }

        textarea.form-control { min-height: 120px; resize: vertical; }

        .form-control-static {
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
            color: var(--text-color);
            background-color: var(--background-light);
            border: 1px solid var(--border-color-light);
            border-radius: 6px;
            display: flex;
            align-items: center;
        }

        .form-control-static .static-icon {
            margin-right: 0.7rem;
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        /* Select2 */
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + 1.6rem + 2px);
            border: 1px solid var(--border-color-dark);
            border-radius: 6px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-color);
            padding-left: 1.2rem;
            padding-right: 2.2rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 1.6rem + 2px);
            right: 8px;
            display: flex;
            align-items: center;
        }

        .form-actions {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color-light);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.8rem 1.8rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-primary { background-color: var(--primary-color); color: #fff; border: 1px solid var(--primary-color); }
        .btn-primary:hover { background-color: #004494; box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2); }
        .btn-secondary { background-color: #fff; color: var(--secondary-color); border: 1px solid var(--border-color-dark); }
        .btn-secondary:hover { background-color: var(--background-light); color: var(--text-color); }
        .btn .fas { margin-right: 0.75rem; }

        /* Tarjeta Cliente */
        #client-info {
            display: none;
            padding: 1rem;
            margin-top: 0.8rem;
            background-color: var(--background-light);
            border: 1px solid var(--border-color-light);
            border-radius: 8px;
        }

        #client-info p {
            margin: 0.4rem 0;
            font-size: 0.9rem;
            color: var(--text-color);
        }

        #client-info i {
            color: var(--primary-color);
            margin-right: 0.4rem;
        }
    </style>
</head>
<body>
<form action="{{ route('interactions.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="corporate-form-container">

        {{-- CLIENTE --}}
        <h3 class="form-section-title"><i class="fas fa-user-tie section-icon"></i> Información del Cliente</h3>
        <div class="form-group">
            <label for="client_id" class="form-label">Cliente <span class="required-asterisk">*</span></label>
            <select name="client_id" id="client_id" class="form-control" required>
                <option value="">Selecciona o busca un cliente...</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->cod_ter }}">{{ $cliente->cod_ter }} - {{ $cliente->apl1 }} {{ $cliente->nom1 }}</option>
                @endforeach
            </select>

            {{-- Tarjeta informativa --}}
            <div id="client-info">
                <p><i class="fas fa-map-marker-alt"></i> <strong>Dirección:</strong> <span id="info-dir"></span></p>
                <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> <span id="info-tel"></span></p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <span id="info-email"></span></p>
                <p><i class="fas fa-city"></i> <strong>Ciudad:</strong> <span id="info-ciudad"></span></p>
            </div>
        </div>

        {{-- AGENTE Y FECHA --}}
        <h3 class="form-section-title"><i class="fas fa-headset section-icon"></i> Detalles del Agente y Fecha</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="form-label">Agente</label>
                <div class="form-control-static"><i class="fas fa-user-tag static-icon"></i> {{ auth()->user()->name }}</div>
                <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
            </div>
            <div class="form-group col-md-6">
                <label class="form-label">Fecha y Hora de Inicio</label>
                @php $now = now(); @endphp
                <div class="form-control-static"><i class="fas fa-clock static-icon"></i> {{ $now->format('d/m/Y h:i A') }}</div>
                <input type="hidden" name="interaction_date" value="{{ $now->toDateTimeString() }}">
            </div>
        </div>

        {{-- DETALLES DE LA INTERACCIÓN --}}
        <h3 class="form-section-title"><i class="fas fa-comments section-icon"></i> Detalles de la Interacción</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="form-label">Canal</label>
                <select name="interaction_channel" class="form-control" required>
                    <option value="">Selecciona un canal...</option>
                    <option>Teléfono</option>
                    <option>WhatsApp</option>
                    <option>Email</option>
                    <option>Presencial</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="form-label">Tipo de Interacción</label>
                <select name="interaction_type" class="form-control" required>
                    <option value="">Selecciona un tipo...</option>
                    <option>Entrante</option>
                    <option>Saliente</option>
                    <option>Seguimiento</option>
                </select>
            </div>
        </div>

        {{-- NOTAS --}}
        <h3 class="form-section-title"><i class="fas fa-clipboard-list section-icon"></i> Notas</h3>
        <div class="form-group">
            <textarea name="notes" class="form-control" rows="5" placeholder="Detalles de la interacción..." required></textarea>
        </div>

        {{-- RESULTADO --}}
        <h3 class="form-section-title"><i class="fas fa-check-circle section-icon"></i> Resultado</h3>
        <div class="form-group">
            <select name="outcome" class="form-control" required>
                <option value="">Selecciona un resultado...</option>
                <option>Concretada</option>
                <option>No contesta</option>
                <option>Pendiente</option>
            </select>
        </div>

        {{-- PRÓXIMA ACCIÓN --}}
        <h3 class="form-section-title"><i class="fas fa-calendar-alt section-icon"></i> Próxima Acción (Opcional)</h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <input type="datetime-local" name="next_action_date" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <select name="next_action_type" class="form-control">
                    <option value="">Selecciona un tipo...</option>
                    <option>Volver a llamar</option>
                    <option>Enviar WhatsApp</option>
                    <option>Reunión</option>
                </select>
            </div>
        </div>

        {{-- ADJUNTOS --}}
        <h3 class="form-section-title"><i class="fas fa-paperclip section-icon"></i> Adjuntos</h3>
        <div class="form-group">
            <input type="file" name="attachments[]" class="form-control-file" multiple>
            <small class="form-text">Formatos: PDF, JPG, PNG, DOCX, XLSX</small>
        </div>

        {{-- URL --}}
        <h3 class="form-section-title"><i class="fas fa-link section-icon"></i> Enlace Externo</h3>
        <div class="form-group">
            <input type="url" name="interaction_url" class="form-control" placeholder="https://...">
        </div>

        {{-- ACCIONES --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('interactions.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle"></i> Cancelar</a>
        </div>

    </div>
</form>

<script>
    $(document).ready(function () {
        $('#client_id').select2({
            placeholder: "Escribe aquí para buscar un cliente...",
            allowClear: true,
            width: '100%',
            language: { noResults: () => "No se encontraron clientes que coincidan" }
        });

        // Al cambiar el cliente seleccionado
        $('#client_id').on('change', function () {
            let cod_ter = $(this).val();

            if (cod_ter) {
                $.ajax({
                    url: `/interactions/cliente/${cod_ter}`,
                    type: 'GET',
                    success: function (data) {
                        $('#info-dir').text(data.dir ?? 'No disponible');
                        $('#info-tel').text(data.tel1 ?? 'No disponible');
                        $('#info-email').text(data.email ?? 'No disponible');
                        $('#info-ciudad').text(data.ciudad ?? 'No disponible');
                        $('#client-info').show();
                    },
                    error: function () {
                        $('#client-info').hide();
                    }
                });
            } else {
                $('#client-info').hide();
            }
        });
    });
</script>
</body>
</html>
