<x-base-layout>
    <div class="maintenance-vault-wrapper">
        <div class="container-xxl">
            
            {{-- Header de Seguridad --}}
            <div class="system-status-bar mb-10 animate-fade-in">
                <div class="d-flex align-items-center gap-4">
                    <span class="status-dot pulse-success"></span>
                    <span class="text-uppercase fw-bolder text-white fs-9 tracking-widest">Master System Active</span>
                    <div class="divider-vertical"></div>
                    <span class="badge badge-outline-primary fs-10 fw-bold">ID: AWS-RDS-MASTER</span>
                    <span class="badge badge-outline-warning fs-10 fw-bold">ENCRYPT: AES-256</span>
                </div>
            </div>

            <div class="row align-items-center justify-content-center min-vh-75">
                <div class="col-lg-7 text-center">
                    
                    {{-- Icono Central Tech --}}
                    <div class="main-tech-icon mb-8">
                        <div class="icon-circle">
                            <i class="bi bi-shield-lock-fill text-primary display-3 shadow-glow"></i>
                        </div>
                        <div class="orbit-container">
                            <div class="orbit-path"></div>
                            <div class="orbit-electron"></div>
                        </div>
                    </div>

                    {{-- Título Principal --}}
                    <h1 class="display-4 fw-bolder text-white mb-3">MANTENIMIENTO ESTRUCTURAL</h1>
                    <p class="text-primary-light fs-5 mb-10 fw-light">
                        El módulo de contabilidad está siendo optimizado para sincronización masiva con AWS.
                    </p>

                    {{-- Tarjeta Glassmorphism --}}
                    <div class="glass-card text-start p-8 p-lg-12">
                        <div class="row g-5">
                            <div class="col-md-6">
                                <h5 class="text-white fw-bold mb-5 d-flex align-items-center">
                                    <i class="bi bi-cpu me-3 text-primary"></i> Estado del Núcleo
                                </h5>
                                <div class="d-flex flex-column gap-3">
                                    <div class="stat-item">
                                        <span class="text-muted fs-8">MOTOR SQL</span>
                                        <span class="text-white fs-8 fw-bold">OPTIMIZANDO ÍNDICES</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="text-muted fs-8">HASH SYNC</span>
                                        <span class="text-warning fs-8 fw-bold">PENDIENTE VALIDACIÓN</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="text-muted fs-8">MEMORIA RAM</span>
                                        <span class="text-success fs-8 fw-bold">ESTABLE (512MB)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-white fw-bold mb-5 d-flex align-items-center">
                                    <i class="bi bi-terminal me-3 text-primary"></i> Consola de Logs
                                </h5>
                                <div class="terminal-box p-4 font-monospace">
                                    <div class="log-line text-success">>> Initializing master_sync.sh...</div>
                                    <div class="log-line text-white">>> Checking AWS RDS connection... OK</div>
                                    <div class="log-line text-white">>> Loading 100k records to buffer...</div>
                                    <div class="log-line text-warning">>> Running Upsert procedure...</div>
                                    <div class="log-line text-primary animate-blink">>> Waiting for admin confirm... _</div>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de Progreso --}}
                        <div class="mt-10 pt-5 border-top border-white-10">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fs-9 fw-bold">PROCEDIMIENTO GLOBAL DE RESTAURACIÓN</span>
                                <span class="text-primary fs-9 fw-bold">64%</span>
                            </div>
                            <div class="progress bg-dark-soft h-5px">
                                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 64%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="mt-12 d-flex flex-wrap justify-content-center gap-4">
                        <a href="{{ route('contabilidad.sincronizar.index') }}" class="btn btn-primary btn-lg px-10 rounded-pill shadow-blue">
                            <i class="bi bi-gear-fill me-2"></i> Abrir Consola Maestra
                        </a>
                        <a href="/" class="btn btn-outline-white btn-lg px-10 rounded-pill">
                            Ir al Dashboard General
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos Base de la Bóveda de Mantenimiento */
        .maintenance-vault-wrapper {
            background: radial-gradient(circle at 50% -20%, #1e1e2d 0%, #0d0d12 80%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }

        .text-primary-light { color: #a5d5ff; }
        .min-vh-75 { min-height: 75vh; }
        .tracking-widest { letter-spacing: 0.2em; }
        .border-white-10 { border-color: rgba(255,255,255,0.1) !important; }

        /* Status Bar */
        .system-status-bar {
            background: rgba(255,255,255,0.03);
            padding: 12px 25px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.05);
            display: inline-block;
        }
        .divider-vertical { width: 1px; height: 15px; background: rgba(255,255,255,0.1); }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; }
        .pulse-success { background: #50cd89; box-shadow: 0 0 10px #50cd89; animation: pulse 2s infinite; }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            background: rgba(0,0,0,0.2);
            padding: 8px 15px;
            border-radius: 8px;
        }

        /* Terminal Box */
        .terminal-box {
            background: #000;
            border-radius: 12px;
            height: 140px;
            font-size: 10px;
            border: 1px solid #333;
            overflow: hidden;
        }
        .log-line { margin-bottom: 4px; }

        /* Icono Orbit */
        .main-tech-icon { position: relative; display: inline-block; }
        .icon-circle {
            width: 120px; height: 120px;
            background: rgba(0, 158, 247, 0.05);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid rgba(0, 158, 247, 0.2);
        }
        .shadow-glow { filter: drop-shadow(0 0 15px #009ef7); }
        .orbit-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; animation: rotate 10s linear infinite; }
        .orbit-path { position: absolute; width: 160px; height: 160px; border: 1px dashed rgba(255,255,255,0.1); border-radius: 50%; top: -20px; left: -20px; }
        .orbit-electron { position: absolute; width: 8px; height: 8px; background: #009ef7; border-radius: 50%; top: -24px; left: 50%; box-shadow: 0 0 10px #009ef7; }

        /* Botones */
        .btn-outline-white { border: 1px solid rgba(255,255,255,0.2); color: white; }
        .btn-outline-white:hover { background: rgba(255,255,255,0.1); color: white; }
        .shadow-blue { box-shadow: 0 10px 20px -5px rgba(0, 158, 247, 0.5); }

        /* Animaciones */
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes blink { 50% { opacity: 0; } }
        .animate-blink { animation: blink 1s infinite; }
        .animate-fade-in { animation: fadeIn 1s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        .progress { background: rgba(255,255,255,0.05); }
    </style>
</x-base-layout>