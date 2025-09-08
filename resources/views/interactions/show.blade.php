<x-base-layout>
    <div class="container">
        <h1 class="title">Detalle de la Interacción</h1>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Tabla de detalles --}}
        <div class="table-responsive">
            <table>
                <tr><th>ID</th><td>{{ $interaction->id }}</td></tr>
                <tr>
                    <th>Cliente</th>
                    <td>
                        @if($interaction->client)
                            <strong>{{ $interaction->client->cod_ter }}</strong> - 
                            {{ $interaction->client->apl1 }} {{ $interaction->client->apl2 }} 
                            {{ $interaction->client->nom1 }} {{ $interaction->client->nom2 }}
                        @else N/A @endif
                    </td>
                </tr>
                <tr><th>Agente</th><td>{{ $interaction->agent->name ?? 'Sin asignar' }}</td></tr>
                <tr><th>Fecha</th><td>{{ $interaction->interaction_date->format('d/m/Y H:i') }}</td></tr>
                <tr><th>Tipo</th><td>{{ $interaction->interaction_type }}</td></tr>
                <tr><th>Resultado</th><td>{{ $interaction->outcome }}</td></tr>
                <tr><th>Notas</th><td>{{ $interaction->notes ?? 'N/A' }}</td></tr>
                <tr>
                    <th>Archivos Adjuntos</th>
                    <td>
                        @if($interaction->attachment_urls)
                            @foreach($interaction->attachment_urls as $file)
                                <a href="{{ route('interactions.view', basename($file)) }}" target="_blank">{{ basename($file) }}</a><br>
                            @endforeach
                        @else N/A @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Selector de rango para gráfico --}}
        <form method="GET" action="{{ route('interactions.show', $interaction) }}" style="margin:20px 0;">
            <label for="range">Ver por:</label>
            <select name="range" id="range" onchange="this.form.submit()">
                <option value="day" {{ $range == 'day' ? 'selected' : '' }}>Día</option>
                <option value="month" {{ $range == 'month' ? 'selected' : '' }}>Mes</option>
                <option value="year" {{ $range == 'year' ? 'selected' : '' }}>Año</option>
            </select>
        </form>

        {{-- Gráfico minimalista --}}
        <div style="margin-top:15px;">
            <canvas id="agentChart" style="max-height:250px;"></canvas>
        </div>

        {{-- Histórico de interacciones del cliente --}}
        <div style="margin-top:40px;">
            <h2 class="title">Histórico de Interacciones del Cliente</h2>

            @if($clientHistory->isEmpty())
                <p>No hay interacciones anteriores registradas para este cliente.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Agente</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Resultado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientHistory as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>{{ $history->agent->name ?? 'Sin asignar' }}</td>
                                    <td>{{ $history->interaction_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $history->interaction_type }}</td>
                                    <td>{{ $history->outcome }}</td>
                                    <td>
                                        <a href="{{ route('interactions.show', $history) }}" class="btn-create" style="padding:4px 8px; font-size:12px;">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>


        {{-- Botón regresar --}}
        <div style="margin-top:25px;">
            <a href="{{ route('interactions.index') }}" class="btn-create">
                <i class="fas fa-arrow-left"></i> Volver a Interacciones
            </a>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('agentChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, '#FF9F1C'); // naranja cálido
        gradient.addColorStop(1, '#FFBF69'); // amarillo suave

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Interacciones',
                    data: @json($totals),
                    backgroundColor: gradient,
                    borderRadius: 6,
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Trabajo de {{ $interaction->agent->name ?? "Agente" }}',
                        color: '#2c3e50',
                        font: { size: 16, weight: '500' }
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 8,
                        cornerRadius: 4
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#555', font: { size: 12 } },
                        grid: { color: '#dee2e6', borderDash: [3, 3] }
                    },
                    x: {
                        ticks: { color: '#555', font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</x-base-layout>
