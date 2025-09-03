<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Interacciones</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn-create { background-color: #007bff; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; margin-bottom: 20px; display: inline-block;}
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 5px; text-decoration: none; }
        .actions button { background: none; border: none; color: #e3342f; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>
    <x-base-layout>


    <div class="container mx-auto my-6 p-4 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4">Control de llamadas</h2>

        <!-- Iframe con la página de Google Apps Script -->
        <iframe 
            src="https://script.google.com/macros/s/AKfycbyoNT3TI22RfgBSrTvnLHkjVoLuB9maQl68ScCOMJ9jztMCkQ09B6i6Q3wRaxWqStGp/exec"
            width="100%" 
            height="700px" 
            style="border:1px solid #ccc; border-radius:12px;"
            loading="lazy"
            sandbox="allow-scripts allow-forms allow-same-origin allow-popups">
        </iframe>
    </div>


    <div class="container mx-auto my-6 p-4 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4">Gestor de Actividades</h2>

        <!-- Iframe con la página de Google Apps Script -->
        <iframe 
            src="https://script.google.com/macros/s/AKfycbysCqWOvjWLZkpPA1xWrQjboPljFS7Lec0LuzivQWGrvNGO5CoBqEcEcHieMn4MouWXCg/exec"
            width="100%" 
            height="700px" 
            style="border:1px solid #ccc; border-radius:12px;"
            loading="lazy"
            sandbox="allow-scripts allow-forms allow-same-origin allow-popups">
        </iframe>
    </div>


    <div class="container">
        <h1>Lista de Interacciones</h1>

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

        <a href="{{ route('interactions.create') }}" class="btn-create">Crear Nueva Interacción</a>

        @if ($interactions->isEmpty())
            <p>No hay interacciones para mostrar.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Agente</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Resultado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($interactions as $interaction)
                        <tr>
                            <td>{{ $interaction->id }}</td>
                            <td>{{ $interaction->client->name ?? 'N/A' }}</td> {{-- Asumiendo relación con Cliente --}}
                            <td>{{ $interaction->agent->name ?? 'N/A' }}</td>   {{-- Asumiendo relación con Agente --}}
                            <td>{{ $interaction->interaction_date }}</td>
                            <td>{{ $interaction->interaction_type }}</td>
                            <td>{{ $interaction->outcome }}</td>
                            <td class="actions">
                                <a href="{{ route('interactions.edit', $interaction) }}">Editar</a>
                                <form action="{{ route('interactions.destroy', $interaction) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE') {{-- ¡Importante para Laravel simule un DELETE! --}}
                                    <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar esta interacción?');">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>







    
    </x-base-layout>
</body>
</html>