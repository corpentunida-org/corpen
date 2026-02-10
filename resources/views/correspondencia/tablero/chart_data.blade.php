{{-- resources/views/correspondencia/tablero/chart_data.blade.php --}}

<script>
    document.addEventListener("DOMContentLoaded", function() {
        /**
         * 1. CONFIGURACIÓN GLOBAL DE CHART.JS
         */
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.borderRadius = 8;

        /**
         * 2. GRÁFICA: DISTRIBUCIÓN POR ESTADO (Doughnut)
         */
        const ctxDist = document.getElementById('chartDistribution');
        if (ctxDist) {
            new Chart(ctxDist, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartDistribucion['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartDistribucion['data']) !!},
                        backgroundColor: [
                            '#4f46e5', // Indigo (Principal)
                            '#10b981', // Esmeralda (Completado)
                            '#f59e0b', // Ámbar (Pendiente)
                            '#3b82f6', // Azul (En trámite)
                            '#ef4444', // Rojo (Vencido)
                            '#94a3b8'  // Gris (Archivado)
                        ],
                        hoverOffset: 15,
                        borderWidth: 4,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12, weight: '600' }
                            }
                        }
                    }
                }
            });
        }

        /**
         * 3. GRÁFICA: CARGA OPERATIVA POR USUARIO (Bar)
         */
        const ctxLoad = document.getElementById('chartWorkload');
        if (ctxLoad) {
            new Chart(ctxLoad, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartCarga['labels']) !!},
                    datasets: [{
                        label: 'Pendientes',
                        data: {!! json_encode($chartCarga['data']) !!},
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        hoverBackgroundColor: '#4f46e5',
                        borderRadius: 6,
                        barThickness: 25
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Gráfica horizontal para mejor lectura de nombres
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: { stepSize: 1 }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { weight: '700' }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` Radicados asignados: ${context.parsed.x}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    /**
     * FUNCIONES ADICIONALES DE INTERACCIÓN
     */
    function updateCharts(newData) {
        // Esta función se puede llamar vía AJAX para refrescar las gráficas
        // sin recargar la página completa si decides implementar filtros asíncronos.
        console.log("Actualizando indicadores...");
    }
</script>

<style>
    /* Ajustes específicos para que los canvas se vean premium */
    canvas {
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.02));
    }
</style>