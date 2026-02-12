{{-- resources/views/correspondencia/tablero/chart_data.blade.php --}}

<script>
    document.addEventListener("DOMContentLoaded", function() {
        /**
         * 1. CONFIGURACIÓN GLOBAL Y UTILIDADES
         */
        const themeColors = {
            indigo: '#4f46e5',
            emerald: '#10b981',
            amber: '#f59e0b',
            blue: '#3b82f6',
            rose: '#f43f5e',
            slate: '#94a3b8',
            grid: '#f1f5f9'
        };

        Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.borderRadius = 10;
        Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: '700' };
        Chart.defaults.plugins.tooltip.usePointStyle = true;

        /**
         * 2. GRÁFICA: DISTRIBUCIÓN POR ESTADO
         * Mejora: Añadido de gradientes y efectos de hover dinámicos
         */
        const ctxDist = document.getElementById('chartDistribution');
        if (ctxDist) {
            const distChart = new Chart(ctxDist, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartDistribucion['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartDistribucion['data']) !!},
                        backgroundColor: [
                            themeColors.indigo,
                            themeColors.emerald,
                            themeColors.amber,
                            themeColors.blue,
                            themeColors.rose,
                            themeColors.slate
                        ],
                        hoverOffset: 20,
                        borderWidth: 5,
                        borderColor: '#ffffff',
                        borderRadius: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    cutout: '75%',
                    responsive: true,
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 25,
                                font: { size: 12, weight: '600' },
                                generateLabels: (chart) => {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            return {
                                                text: `${label} (${value})`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                strokeStyle: '#fff',
                                                lineWidth: 0,
                                                hidden: isNaN(data.datasets[0].data[i]) || chart.getDatasetMeta(0).data[i].hidden,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.raw / total) * 100).toFixed(1);
                                    return ` Cantidad: ${context.raw} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        /**
         * 3. GRÁFICA: CARGA OPERATIVA POR USUARIO
         * Mejora: Gradiente lineal en las barras para un look moderno
         */
        const ctxLoad = document.getElementById('chartWorkload');
        if (ctxLoad) {
            // Crear gradiente para las barras
            const gradient = ctxLoad.getContext('2d').createLinearGradient(0, 0, 400, 0);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.8)');
            gradient.addColorStop(1, 'rgba(129, 140, 248, 1)');

            new Chart(ctxLoad, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartCarga['labels']) !!},
                    datasets: [{
                        label: 'Pendientes',
                        data: {!! json_encode($chartCarga['data']) !!},
                        backgroundColor: gradient,
                        hoverBackgroundColor: themeColors.indigo,
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 20,
                        maxBarThickness: 30
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { display: false },
                            border: { display: false },
                            ticks: { 
                                stepSize: 1,
                                font: { size: 11 }
                            }
                        },
                        y: {
                            grid: { 
                                color: themeColors.grid,
                                drawTicks: false 
                            },
                            border: { display: false },
                            ticks: {
                                font: { size: 12, weight: '700' },
                                padding: 10
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            displayColors: false,
                            callbacks: {
                                title: (items) => `Responsable: ${items[0].label}`,
                                label: (item) => `📂 ${item.raw} radicados activos`
                            }
                        }
                    },
                    layout: {
                        padding: { left: 0, right: 20, top: 0, bottom: 0 }
                    }
                }
            });
        }
    });

    /**
     * Gestión de redimensionamiento para asegurar calidad visual
     */
    window.addEventListener('resize', () => {
        Chart.instances.forEach(chart => {
            chart.resize();
        });
    });
</script>

<style>
    /* Estilización del contenedor para evitar saltos de layout */
    canvas {
        transition: opacity 0.3s ease;
    }
    
    #chartDistribution, #chartWorkload {
        max-width: 100% !important;
        filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.02));
    }

    /* Animación de entrada para los gráficos */
    .chart-canvas-wrapper {
        animation: chartFadeIn 0.6s ease-out;
    }

    @keyframes chartFadeIn {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }
</style>