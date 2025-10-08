<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0V4LLanw2qksYuGWV8D5W0X/d4j7tL3n+t+S4/K/aK/q8Cg2F5F5F5F5Fw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="corporate-form-container">

    {{-- Errores --}}
    @if ($errors->any())
        <div class="form-alert form-alert-error">
            <i class="fas fa-exclamation-circle alert-icon"></i>
            <strong>Error:</strong> Por favor, revisa los campos señalados.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CLIENTE --}}
    <h3 class="form-section-title"><i class="fas fa-user-tie section-icon"></i> Información del Cliente</h3>
    <div class="form-group">
        <label for="client_id" class="form-label">Cliente <span class="required-asterisk">*</span></label>
        <select name="client_id" id="client_id" class="form-control" required>
            <option value="">Selecciona o busca un cliente...</option> 
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->cod_ter }}"
                    {{ old('client_id', $interaction->client_id) == $cliente->cod_ter ? 'selected' : '' }}>
                    {{ $cliente->cod_ter }} - {{ $cliente->apl1 }} {{ $cliente->apl2 }} {{ $cliente->nom1 }} {{ $cliente->nom2 }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- AGENTE Y FECHA --}}
    <h3 class="form-section-title"><i class="fas fa-headset section-icon"></i> Detalles del Agente y Fecha</h3>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="agent_id" class="form-label">Agente</label>
            <div class="form-control-static"><i class="fas fa-user-tag static-icon"></i> {{ $interaction->agent->name ?? auth()->user()->name ?? 'Sin asignar' }}</div>
            <input type="hidden" name="agent_id" value="{{ $interaction->agent_id ?? auth()->user()->id }}">
        </div>
        <div class="form-group col-md-6">
            <label for="interaction_date" class="form-label">Fecha y Hora de Inicio</label>
            @php $interactionDate = $interaction->interaction_date ?? now(); @endphp
            <div class="form-control-static"><i class="fas fa-clock static-icon"></i>
                {{ $interactionDate->format('d/m/Y h:i A') }}
            </div>
            <input type="hidden" name="interaction_date" value="{{ $interactionDate->toDateTimeString() }}">
        </div>
    </div>

    {{-- DETALLES DE LA INTERACCIÓN --}}
    <h3 class="form-section-title"><i class="fas fa-comments section-icon"></i> Detalles de la Interacción</h3>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="interaction_channel" class="form-label">Canal <span class="required-asterisk">*</span></label>
            @php $channels = ['Teléfono', 'WhatsApp Call', 'WhatsApp Message', 'Email', 'Presencial', 'Chat Web']; @endphp
            <select name="interaction_channel" id="interaction_channel" class="form-control" required>
                <option value="">Selecciona un canal...</option>
                @foreach ($channels as $channel)
                    <option value="{{ $channel }}" {{ old('interaction_channel', $interaction->interaction_channel) == $channel ? 'selected' : '' }}>
                        {{-- Iconos removidos directamente de aquí para evitar el ParseError --}}
                        {{ $channel }}
                    </option>
                @endforeach
            </select>
        </div>
         <div class="form-group col-md-6">
            <label for="interaction_type" class="form-label">Tipo de Interacción <span class="required-asterisk">*</span></label>
            @php $types = ['Entrante', 'Saliente', 'Seguimiento', 'Cobranza', 'Envío de Información', 'Venta', 'Soporte']; @endphp
            <select name="interaction_type" id="interaction_type" class="form-control" required>
                <option value="">Selecciona un tipo...</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" {{ old('interaction_type', $interaction->interaction_type) == $type ? 'selected' : '' }}>
                        {{-- Iconos removidos directamente de aquí para evitar el ParseError --}}
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="duration" class="form-label">Duración</label>
        <div class="form-control-static"><i class="fas fa-hourglass-half static-icon"></i>
            {{ $interaction->duration ?? 'Calculado al guardar' }}
            @if($interaction->duration) minutos @endif
        </div>
    </div>

    {{-- NOTAS --}}
    <h3 class="form-section-title"><i class="fas fa-clipboard-list section-icon"></i> Notas de la Interacción</h3>
    <div class="form-group">
        <label for="notes" class="form-label">Notas <span class="required-asterisk">*</span></label>
        <textarea name="notes" id="notes" class="form-control" rows="6" required
            placeholder="Detalles, acciones tomadas, seguimiento, observaciones importantes...">{{ old('notes', $interaction->notes) }}</textarea>
    </div>

    {{-- RESULTADO --}}
    <h3 class="form-section-title"><i class="fas fa-check-circle section-icon"></i> Resultado de la Interacción</h3>
    <div class="form-group">
        <label for="outcome" class="form-label">Resultado <span class="required-asterisk">*</span></label>
        @php $outcomes = ['Concretada', 'No contesta', 'Mensaje Enviado', 'Email Enviado', 'Compromiso de pago', 'Leído', 'Pendiente', 'Resuelto']; @endphp
        <select name="outcome" id="outcome" class="form-control" required>
            <option value="">Selecciona un resultado...</option>
            @foreach ($outcomes as $outcome)
                <option value="{{ $outcome }}" {{ old('outcome', $interaction->outcome) == $outcome ? 'selected' : '' }}>
                     {{-- Iconos removidos directamente de aquí para evitar el ParseError --}}
                     {{ $outcome }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PRÓXIMA ACCIÓN --}}
    <h3 class="form-section-title"><i class="fas fa-calendar-alt section-icon"></i> Próxima Acción (Opcional)</h3>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="next_action_date" class="form-label small-label">Fecha</label>
            <input type="datetime-local" name="next_action_date" id="next_action_date" class="form-control"
                value="{{ old('next_action_date', $interaction->next_action_date ? \Carbon\Carbon::parse($interaction->next_action_date)->format('Y-m-d\TH:i') : '') }}">
        </div>
        <div class="form-group col-md-6">
            <label for="next_action_type" class="form-label small-label">Tipo</label>
            @php $nextActionTypes = ['Volver a llamar', 'Enviar WhatsApp', 'Enviar Email', 'Visita', 'Reunión']; @endphp
            <select name="next_action_type" id="next_action_type" class="form-control">
                <option value="">Selecciona un tipo...</option>
                @foreach ($nextActionTypes as $nat)
                    <option value="{{ $nat }}" {{ old('next_action_type', $interaction->next_action_type) == $nat ? 'selected' : '' }}>
                        {{-- Iconos removidos directamente de aquí para evitar el ParseError --}}
                        {{ $nat }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="next_action_notes" class="form-label small-label">Notas Próxima Acción</label>
        <textarea name="next_action_notes" id="next_action_notes" class="form-control" rows="3" 
            placeholder="Detalles del seguimiento..."
            >{{ old('next_action_notes', $interaction->next_action_notes) }}</textarea>
    </div>

    {{-- ADJUNTOS --}}
    <h3 class="form-section-title"><i class="fas fa-paperclip section-icon"></i> Documentos Adjuntos</h3>
    <div class="form-group">
        <label for="attachments" class="form-label">Subir Archivos</label>
        <input type="file" name="attachments[]" id="attachments" class="form-control-file" multiple>
        <small class="form-text text-muted">Formatos permitidos: PDF, JPG, PNG, DOCX, XLSX. Tamaño máximo por archivo: 5MB.</small>
        @if($interaction->attachment_urls && count($interaction->attachment_urls) > 0)
            <div class="existing-attachments mt-3">
                <p class="form-label">Archivos existentes:</p>
                <ul class="list-unstyled">
                    @foreach($interaction->attachment_urls as $path)
                        <li><i class="fas fa-file-alt mr-2"></i><a href="{{ route('interactions.view', basename($path)) }}" target="_blank">{{ basename($path) }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- URL --}}
    <h3 class="form-section-title"><i class="fas fa-link section-icon"></i> Enlace Externo</h3>
    <div class="form-group">
        <label for="interaction_url" class="form-label">URL (Grabación, Chat, etc.)</label>
        <input type="url" name="interaction_url" id="interaction_url" class="form-control" 
            value="{{ old('interaction_url', $interaction->interaction_url) }}"
            placeholder="Ej: https://link-a-grabacion.com/123 o enlace a chat/documento">
        <small class="form-text text-muted">Enlace a grabaciones, chats de WhatsApp, o cualquier recurso externo relevante.</small>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Guardar Interacción</button>
        <a href="{{ route('interactions.index') }}" class="btn btn-secondary"><i class="fas fa-times-circle mr-2"></i> Cancelar</a>
    </div>
</div>

{{-- Estilos Corporativos --}}
<style>
    :root {
        --primary-color: #0056b3; /* Azul corporativo */
        --secondary-color: #6c757d; /* Gris para secundarios */
        --accent-color: #28a745; /* Verde para éxito/confirmación */
        --danger-color: #dc3545; /* Rojo para errores */
        --text-color: #343a40; /* Texto oscuro */
        --light-text-color: #6f7478; /* Texto más claro para notas */
        --border-color-light: #e9ecef;
        --border-color-dark: #ced4da;
        --background-light: #f8f9fa;
        --input-bg: #ffffff;
        --focus-ring: rgba(0, 86, 179, 0.25);
    }

    .corporate-form-container {
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        font-size: 0.95rem;
        color: var(--text-color);
        background-color: var(--background-light);
        padding: 2.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        max-width: 900px;
        margin: 2rem auto;
        border: 1px solid var(--border-color-light);
    }

    /* Títulos de Sección */
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

    .form-section-title .section-icon {
        margin-right: 0.8rem;
        color: var(--primary-color);
        font-size: 1.4rem;
    }

    /* Grupos de Formulario */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }

    .form-row .form-group {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        flex: 1; /* Ocupa el espacio disponible */
        min-width: 250px; /* Asegura que no se haga demasiado pequeño */
    }

    @media (min-width: 768px) {
        .form-row .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    /* Labels */
    .form-label {
        display: block;
        margin-bottom: 0.6rem;
        font-weight: 600;
        color: var(--text-color);
        font-size: 0.9rem;
    }

    .form-label.small-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--light-text-color);
    }

    .required-asterisk {
        color: var(--danger-color);
        margin-left: 0.25rem;
        font-weight: normal;
    }

    /* Controles de Formulario */
    .form-control,
    .form-control-file {
        display: block;
        width: 100%;
        padding: 0.8rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 400;
        line-height: 1.5;
        color: var(--text-color);
        background-color: var(--input-bg);
        background-clip: padding-box;
        border: 1px solid var(--border-color-dark);
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        appearance: none; /* Para estandarizar el estilo en distintos navegadores */
    }

    .form-control:focus {
        color: var(--text-color);
        background-color: var(--input-bg);
        border-color: var(--primary-color);
        outline: 0;
        box-shadow: 0 0 0 0.25rem var(--focus-ring);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    /* Display estático para campos no editables */
    .form-control-static {
        padding: 0.8rem 1.2rem;
        font-size: 0.95rem;
        color: var(--text-color);
        background-color: var(--background-light);
        border: 1px solid var(--border-color-light);
        border-radius: 6px;
        min-height: calc(1.5em + 1.6rem + 2px); /* Alinea con los inputs */
        display: flex;
        align-items: center;
    }

    .form-control-static .static-icon {
        margin-right: 0.7rem;
        color: var(--secondary-color);
        font-size: 1.1rem;
    }

    /* Select2 overrides */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 1.6rem + 2px); /* Ajustar altura al resto de inputs */
        border: 1px solid var(--border-color-dark);
        border-radius: 6px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-color);
        line-height: 1.5; /* Ajustar line-height */
        padding-left: 1.2rem;
        padding-right: 2.2rem; /* Espacio para la flecha */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 1.6rem + 2px);
        right: 8px;
        display: flex;
        align-items: center;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default .select2-selection--single:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem var(--focus-ring);
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: var(--primary-color);
        color: #fff;
    }

    .select2-dropdown {
        border: 1px solid var(--border-color-dark);
        border-radius: 6px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border-color-dark);
        padding: 0.5rem;
        border-radius: 4px;
    }

    /* Mensajes de Alerta */
    .form-alert {
        padding: 1rem 1.5rem;
        margin-bottom: 1.8rem;
        border-radius: 6px;
        display: flex;
        align-items: flex-start;
        font-size: 0.9rem;
    }

    .form-alert-error {
        background-color: #fcebeb; /* Rojo claro */
        border: 1px solid #f0b4b4;
        color: var(--danger-color);
    }

    .form-alert-error .alert-icon {
        color: var(--danger-color);
        margin-right: 1rem;
        font-size: 1.2rem;
        line-height: 1.4;
    }

    .form-alert ul {
        margin: 0.5rem 0 0 1.5rem;
        padding: 0;
        list-style-type: disc;
    }
    .form-alert ul li {
        margin-bottom: 0.2rem;
    }

    /* Texto Pequeño / Ayuda */
    .form-text {
        font-size: 0.85rem;
        color: var(--light-text-color);
        margin-top: 0.4rem;
    }

    /* Adjuntos existentes */
    .existing-attachments {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f1f7fd;
        border: 1px solid #d0e7fa;
        border-radius: 6px;
    }

    .existing-attachments p.form-label {
        font-weight: 600;
        margin-bottom: 0.8rem;
        color: var(--primary-color);
    }

    .existing-attachments ul {
        padding-left: 0;
        list-style: none;
    }

    .existing-attachments li {
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .existing-attachments li a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .existing-attachments li a:hover {
        text-decoration: underline;
    }

    .existing-attachments .fa-file-alt {
        color: var(--secondary-color);
        font-size: 1rem;
    }

    /* Botones de Acción */
    .form-actions {
        margin-top: 3rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color-light);
        display: flex;
        justify-content: flex-end;
        gap: 1rem; /* Espacio entre botones */
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.8rem 1.8rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: #fff;
        border: 1px solid var(--primary-color);
    }

    .btn-primary:hover {
        background-color: #004494;
        border-color: #004494;
        box-shadow: 0 4px 8px rgba(0, 86, 179, 0.2);
    }

    .btn-secondary {
        background-color: #ffffff;
        color: var(--secondary-color);
        border: 1px solid var(--border-color-dark);
    }

    .btn-secondary:hover {
        background-color: var(--background-light);
        border-color: var(--secondary-color);
        color: var(--text-color);
    }

    .btn .fas {
        margin-right: 0.75rem;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inicialización de Select2
        $('#client_id').select2({
            placeholder: "Escribe aquí para buscar un cliente...",
            allowClear: true,
            width: '100%',
            language: { noResults: () => "No se encontraron clientes que coincidan" }
        });
    });
</script>
@endpush