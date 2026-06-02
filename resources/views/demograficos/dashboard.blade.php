<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-chart-pie me-1"></i> Módulo de Demografía</span>
                <h1 class="main-title">Tablero de Control Geográfico</h1>
                <p class="main-subtitle">Métricas generales del maestro de países, regiones y ciudades.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('demograficos.sincronizar.index') }}" class="btn-ghost-corporate me-2">
                    <i class="fas fa-sync-alt"></i> <span>Sincronización Excel</span>
                </a>
                <a href="{{ route('demograficos.maestro.index') }}" class="btn-corporate-black">
                    <i class="fas fa-list"></i> <span>Ver Maestro</span>
                </a>
            </div>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="dash-widget border-bottom-info">
                    <div class="widget-icon bg-info-light text-info"><i class="fas fa-globe"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Países Registrados</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $totalPaises }}</h3> <span class="badge bg-info text-dark shadow-sm">Global</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-widget border-bottom-success">
                    <div class="widget-icon bg-success-light text-success"><i class="fas fa-map"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Regiones / Departamentos</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $totalRegiones }}</h3> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-widget border-bottom-warning">
                    <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-city"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Ciudades Activas</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $totalCiudades }}</h3> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dash-widget border-bottom-danger">
                    <div class="widget-icon bg-danger-light text-danger"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Direcciones</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $totalDirecciones }}</h3> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @includeIf('asociados.partials.styles')
</x-base-layout>