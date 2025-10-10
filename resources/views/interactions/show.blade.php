<x-base-layout>

<style>
    /* === Estilos Pastel Modernos === */
    :root {
        --primary-pastel: #A2D2FF;
        --secondary-pastel: #BDE0FE;
        --success-pastel: #C7F9CC;
        --error-pastel: #FFC7B2;
        --warning-pastel: #FFECB3;
        --text-dark: #495057;
        --text-light: #6c757d;
        --background-light: #F8F9FA;
        --card-bg: #FFFFFF;
        --border-light: #e9ecef;
        --shadow-subtle: 0 2px 10px rgba(0,0,0,0.05);
    }

    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        background-color: var(--background-light);
    }

    .container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-subtle);
        overflow: hidden;
        transition: all 0.2s ease-in-out;
    }

    .card-header {
        background-color: var(--background-light);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header .title {
        margin: 0;
        font-size: 1.15rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    .card-body {
        padding: 1.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .page-header .title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .btn-action {
        background-color: var(--primary-pastel);
        color: var(--text-dark);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.2s ease;
        font-weight: 500;
    }

    .btn-action:hover {
        background-color: #8ac0f5;
    }

    .alert {
        padding: 0.75rem 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }

    .alert-success {
        background-color: var(--success-pastel);
        color: #285a30;
        border: 1px solid #9adea4;
    }

    .alert-error {
        background-color: var(--error-pastel);
        color: #7b2a2a;
        border: 1px solid #f29c87;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.95rem;
    }

    .detail-item i {
        color: var(--primary-pastel);
        font-size: 1.2rem;
        width: 25px;
        text-align: center;
        padding-top: 3px;
    }

    .detail-item .content strong {
        display: block;
        color: var(--text-light);
        font-size: 0.8rem;
        margin-bottom: 0.15rem;
        font-weight: 500;
    }

    .attachment-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .attachment-list a {
        background-color: var(--secondary-pastel);
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        text-decoration: none;
        color: var(--text-dark);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
    }

    .attachment-list a:hover {
        background-color: #9acafc;
    }

    hr {
        border: none;
        border-top: 1px solid var(--border-light);
        margin: 1.5rem 0;
    }

    .chart-filter {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .chart-filter select {
        padding: 0.4rem 0.7rem;
        border-radius: 6px;
        border: 1px solid var(--border-light);
        background-color: var(--card-bg);
        font-size: 0.9rem;
        color: var(--text-dark);
        padding-right: 2rem;
    }

    .history-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 1rem;
    }

    .history-table th,
    .history-table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-light);
    }

    .history-table thead th {
        background-color: var(--background-light);
        color: var(--text-light);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .history-table tbody tr:hover {
        background-color: #f0f3f6;
    }

    .history-table tbody tr.current-interaction {
        background-color: var(--warning-pastel);
        font-weight: 600;
        color: #6a4f00;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1 class="title">Detalle de Interacción</h1>
        <a href="{{ route('interactions.index') }}" class="btn-action">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

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

    <div class="card">
        <div class="card-header">
            <h2 class="title">Interacción #{{ $interaction->id }}</h2>
        </div>
        <div class="card-body">
            <div class="details-grid">
                <div class="detail-item"><i class="fas fa-user"></i>
                    <div class="content">
                        <strong>Cliente</strong>
                        @if($interaction->client)
                            <span><strong>{{ $interaction->client->cod_ter }}</strong> - {{ $interaction->client->apl1 }} {{ $interaction->client->nom1 }}</span>
                        @else
                            <span>N/A</span>
                        @endif
                    </div>
                </div>
                <div class="detail-item"><i class="fas fa-headset"></i>
                    <div class="content">
                        <strong>Agente</strong>
                        <span>{{ $interaction->agent->name ?? 'Sin asignar' }}</span>
                    </div>
                </div>
                <div class="detail-item"><i class="fas fa-calendar-alt"></i>
                    <div class="content">
                        <strong>Fecha y Hora</strong>
                        <span>{{ $interaction->interaction_date->format('d/m/Y H:i A') }}</span>
                    </div>
                </div>
                <div class="detail-item"><i class="fas fa-tag"></i>
                    <div class="content">
                        <strong>Tipo</strong>
                        <span>{{ $interaction->interaction_type }}</span>
                    </div>
                </div>
                <div class="detail-item"><i class="fas fa-clipboard-check"></i>
                    <div class="content">
                        <strong>Resultado</strong>
                        <span>{{ $interaction->outcome }}</span>
                    </div>
                </div>
            </div>
            <hr>
            <div class="detail-item">
                <i class="fas fa-paperclip"></i>
                <div class="content">
                    <strong>Archivos Adjuntos</strong>
                    <div class="attachment-list">
                        @forelse($interaction->attachment_urls as $file)
                            <a href="{{ route('interactions.view', basename($file)) }}" target="_blank">
                                <i class="fas fa-file-alt"></i> {{ basename($file) }}
                            </a>
                        @empty
                            <span>No hay archivos adjuntos.</span>
                        @endforelse
                    </div>
                </div>
            </div>
            <hr>
            <div class="detail-item">
                <i class="fas fa-pen-to-square"></i>
                <div class="content">
                    <strong>Notas</strong>
                    <p>{{ $interaction->notes ?? 'Sin notas.' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- === Gráfico de Rendimiento del Agente === --}}
    <div class="card">
        <div class="card-header">
            <h2 class="title">Rendimiento del Agente</h2>
        </div>
        <div class="card-body">
            <div class="chart-filter">
                <form method="GET" action="{{ route('interactions.show', $interaction) }}">
                    <label for="range"><strong>Ver por:</strong></label>
                    <select name="range" id="range" onchange="this.form.submit()">
                        <option value="day" {{ $range == 'day' ? 'selected' : '' }}>Día</option>
                        <option value="month" {{ $range == 'month' ? 'selected' : '' }}>Mes</option>
                        <option value="year" {{ $range == 'year' ? 'selected' : '' }}>Año</option>
                    </select>
                </form>
            </div>
            <div style="height: 300px;">
                <canvas id="agentChart"></canvas>
            </div>
        </div>
    </div>

    {{-- === Histórico del Cliente === --}}
    <div class="card">
        <div class="card-header">
            <h2 class="title">Histórico del Cliente</h2>
        </div>
        <div class="card-body">
            @if($clientHistory->isEmpty())
                <p>No hay interacciones anteriores para este cliente.</p>
            @else
                <div class="table-responsive">
                    <table class="history-table">
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
                                <tr class="{{ $history->id == $interaction->id ? 'current-interaction' : '' }}">
                                    <td>{{ $history->id }}</td>
                                    <td>{{ $history->agent->name ?? 'Sin asignar' }}</td>
                                    <td>{{ $history->interaction_date->format('d/m/Y') }}</td>
                                    <td>{{ $history->interaction_type }}</td>
                                    <td>{{ $history->outcome }}</td>
                                    <td>
                                        <a href="{{ route('interactions.show', $history) }}" class="btn-action" style="padding:4px 8px; font-size:0.8rem;">
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('agentChart').getContext('2d');

    const gradientBar = ctx.createLinearGradient(0, 0, 0, 300);
    gradientBar.addColorStop(0, '#BDE0FE');
    gradientBar.addColorStop(1, '#A2D2FF');

    const gradientLine = ctx.createLinearGradient(0, 0, 0, 200);
    gradientLine.addColorStop(0, '#C77DFF');
    gradientLine.addColorStop(1, '#9D4EDD');

    new Chart(ctx, {
        data: {
            labels: @json($labels),
            datasets: [
                {
                    type: 'bar',
                    label: 'Interacciones',
                    data: @json($totals),
                    backgroundColor: gradientBar,
                    borderColor: '#A2D2FF',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    type: 'line',
                    label: 'Promedio de Rendimiento',
                    data: @json($averages ?? array_fill(0, count($labels), 0)),
                    borderColor: '#C77DFF',
                    backgroundColor: gradientLine,
                    fill: false,
                    tension: 0.3,
                    pointBackgroundColor: '#9D4EDD',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Interacciones y Rendimiento de {{ $interaction->agent->name ?? "Agente" }}',
                    color: 'var(--text-dark)',
                    font: { size: 16, weight: '600' }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(73, 80, 87, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 6
                },
                legend: {
                    position: 'top',
                    labels: { color: 'var(--text-light)' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: 'var(--text-light)', stepSize: 1 },
                    grid: { color: 'var(--border-light)', borderDash: [5, 5], drawBorder: false }
                },
                x: {
                    ticks: { color: 'var(--text-light)' },
                    grid: { display: false }
                }
            }
        }
    });
</script>
</x-base-layout>
