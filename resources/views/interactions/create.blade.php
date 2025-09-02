<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Interacción</title>
    {{-- Puedes enlazar tu CSS aquí, por ejemplo, con Vite: @vite('resources/css/app.css') --}}
    <style>
        /* Estilos básicos para la demostración */
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="url"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Para que el padding no aumente el ancho */
        }
        .form-group input[type="file"] { padding: 5px; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .error-message { color: #e3342f; font-size: 0.85em; margin-top: 5px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            float: right;
            margin-top: 10px;
        }
        .btn-submit:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Interacción</h1>

        {{-- Mensajes flash de éxito, error o advertencia --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        <form action="{{ route('interactions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf {{-- ¡Directiva de seguridad CSRF! --}}

            <div class="form-group">
                <label for="client_id">ID Cliente:</label>
                <input type="number" id="client_id" name="client_id" value="{{ old('client_id') }}" required min="1">
                @error('client_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="agent_id">ID Agente:</label>
                <input type="number" id="agent_id" name="agent_id" value="{{ old('agent_id') }}" required min="1">
                @error('agent_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="interaction_date">Fecha de Interacción:</label>
                <input type="date" id="interaction_date" name="interaction_date" value="{{ old('interaction_date') }}" required>
                @error('interaction_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="interaction_channel">Canal:</label>
                <input type="text" id="interaction_channel" name="interaction_channel" value="{{ old('interaction_channel') }}" required>
                @error('interaction_channel')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="interaction_type">Tipo:</label>
                <input type="text" id="interaction_type" name="interaction_type" value="{{ old('interaction_type') }}" required>
                @error('interaction_type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="duration">Duración (minutos):</label>
                <input type="number" id="duration" name="duration" value="{{ old('duration') }}">
                @error('duration')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="outcome">Resultado:</label>
                <input type="text" id="outcome" name="outcome" value="{{ old('outcome') }}" required>
                @error('outcome')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notas:</label>
                <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="parent_interaction_id">Interacción Padre ID (Opcional):</label>
                <input type="number" id="parent_interaction_id" name="parent_interaction_id" value="{{ old('parent_interaction_id') }}">
                @error('parent_interaction_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="next_action_date">Fecha Próxima Acción (Opcional):</label>
                <input type="date" id="next_action_date" name="next_action_date" value="{{ old('next_action_date') }}">
                @error('next_action_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="next_action_type">Tipo Próxima Acción (Opcional):</label>
                <input type="text" id="next_action_type" name="next_action_type" value="{{ old('next_action_type') }}">
                @error('next_action_type')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="next_action_notes">Notas Próxima Acción (Opcional):</label>
                <textarea id="next_action_notes" name="next_action_notes">{{ old('next_action_notes') }}</textarea>
                @error('next_action_notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="interaction_url">URL de Interacción (Opcional):</label>
                <input type="url" id="interaction_url" name="interaction_url" value="{{ old('interaction_url') }}">
                @error('interaction_url')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="attachments">Adjuntos (Imágenes o PDF, máx. 10MB c/u):</label>
                <input type="file" id="attachments" name="attachments[]" multiple>
                @error('attachments')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                @error('attachments.*') {{-- Para errores individuales de cada archivo --}}
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Guardar Interacción</button>
        </form>
    </div>
</body>
</html>