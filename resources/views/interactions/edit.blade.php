<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Interacción</title>
    <style>
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
            box-sizing: border-box;
        }
        .form-group input[type="file"] { padding: 5px; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .error-message { color: #e3342f; font-size: 0.85em; margin-top: 5px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            float: right;
            margin-top: 10px;
        }
        .btn-submit:hover { background-color: #0056b3; }
        .current-attachments { margin-top: 10px; border: 1px solid #eee; padding: 10px; background-color: #f9f9f9; border-radius: 4px; }
        .current-attachments span { display: block; margin-bottom: 5px; }
        .current-attachments a { text-decoration: none; color: #007bff; }
        .current-attachments a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Interacción #{{ $interaction->id }}</h1>

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

        <form action="{{ route('interactions.update', $interaction) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="interaction_date">Fecha de Interacción:</label>
                <input type="date" name="interaction_date" id="interaction_date"
                       value="{{ old('interaction_date', $interaction->interaction_date ? $interaction->interaction_date->format('Y-m-d') : '') }}"
                       class="@error('interaction_date') is-invalid @enderror">
                @error('interaction_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="interaction_type">Tipo de Interacción:</label>
                <input type="text" name="interaction_type" id="interaction_type"
                       value="{{ old('interaction_type', $interaction->interaction_type) }}"
                       class="@error('interaction_type') is-invalid @enderror">
                @error('interaction_type')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="outcome">Resultado:</label>
                <textarea name="outcome" id="outcome" class="@error('outcome') is-invalid @enderror">{{ old('outcome', $interaction->outcome) }}</textarea>
                @error('outcome')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notas:</label>
                <textarea name="notes" id="notes" class="@error('notes') is-invalid @enderror">{{ old('notes', $interaction->notes) }}</textarea>
                @error('notes')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="follow_up_date">Fecha de Seguimiento (Opcional):</label>
                <input type="date" name="follow_up_date" id="follow_up_date"
                       value="{{ old('follow_up_date', $interaction->follow_up_date ? $interaction->follow_up_date->format('Y-m-d') : '') }}"
                       class="@error('follow_up_date') is-invalid @enderror">
                @error('follow_up_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Estado:</label>
                <input type="text" name="status" id="status"
                       value="{{ old('status', $interaction->status) }}"
                       class="@error('status') is-invalid @enderror">
                @error('status')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="priority">Prioridad:</label>
                <input type="text" name="priority" id="priority"
                       value="{{ old('priority', $interaction->priority) }}"
                       class="@error('priority') is-invalid @enderror">
                @error('priority')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="duration">Duración (minutos):</label>
                <input type="number" name="duration" id="duration"
                       value="{{ old('duration', $interaction->duration) }}"
                       class="@error('duration') is-invalid @enderror">
                @error('duration')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="attachments">Archivos Adjuntos:</label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       class="@error('attachments.*') is-invalid @enderror">
                @error('attachments.*')
                    <p class="error-message">{{ $message }}</p>
                @enderror
                @if ($interaction->attachments)
                    <div class="current-attachments">
                        <p>Adjuntos actuales:</p>
                        @if (!empty($interaction->attachments))
                            @foreach ($interaction->attachments as $file)
                                <span><a href="{{ Storage::url($file) }}" target="_blank">{{ basename($file) }}</a></span>
                            @endforeach
                        @else
                            <span>No hay adjuntos actuales.</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="url_reference">URL de Referencia (Opcional):</label>
                <input type="url" name="url_reference" id="url_reference"
                       value="{{ old('url_reference', $interaction->url_reference) }}"
                       class="@error('url_reference') is-invalid @enderror">
                @error('url_reference')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Actualizar Interacción</button>
        </form>
    </div>
</body>
</html>
