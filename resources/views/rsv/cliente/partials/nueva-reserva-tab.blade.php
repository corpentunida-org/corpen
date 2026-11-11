{{-- partials/nueva-reserva-tab.blade.php · Cliente · NIVEL 2 --}}
<div class="reservas-soft p-3 bg-white rounded-3 border tab-content" id="nuevaReservaSubContent" style="border-color:#f1f5f9 !important;">

<style>
    /* ── VARIABLES Y ESTILOS BASE (Paleta Original RSV) ── */
    .reservas-soft { 
        --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; 
        --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; 
        --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; 
    }
    
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); line-height: 1.3; }
    .reservas-soft .strong { color:var(--ink-2); font-weight:600; }
    
    /* ── COMPONENTES VITRINA ── */
    .rsv-hero-card {
        background: linear-gradient(135deg, #3d7a5c 0%, #2e5c43 100%);
        border-radius: 6px;
        padding: 24px 20px;
        position: relative;
        overflow: hidden;
        color: #fff;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(61, 122, 92, 0.15);
    }
    .rsv-hero-card .hero-bg-icon {
        position: absolute;
        right: -15px;
        top: -20px;
        font-size: 8rem;
        opacity: 0.15;
        color: #fff;
        pointer-events: none;
    }
    .rsv-hero-title { font-size: 14px; font-weight: 700; margin-bottom: 6px; position: relative; z-index: 2;}
    .rsv-hero-text { font-size: 9.5px; opacity: 0.85; margin-bottom: 16px; position: relative; z-index: 2;}
    
    .rsv-feature-card {
        background: #fff;
        border: 1px solid var(--grid-2);
        border-radius: 6px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .rsv-feature-card:hover { border-color: var(--accent-bd); box-shadow: 0 4px 12px rgba(0,0,0,.03); background: #fafcfe; }
    .rsv-feature-icon { font-size: 18px; margin-bottom: 8px; }
    
    /* ── BOTONES ── */
    .reservas-soft .btn-light-soft {
        background: rgba(255,255,255,0.9); color: var(--accent); border: none;
        font-size: 8.5px; font-weight: 700; padding: 6px 14px; border-radius: 4px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 4px;
        transition: background 0.2s; position: relative; z-index: 2;
    }
    .reservas-soft .btn-light-soft:hover { background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
</style>

    {{-- ========================================================= --}}
    {{-- SUB-TAB 1: INICIO (LA VITRINA)                            --}}
    {{-- ========================================================= --}}
    <div class="tab-pane fade show active" id="nr-sub-inicio" role="tabpanel">
        
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:6px;">
            <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
                <i class="bi bi-shop me-1" style="font-size:8px; color:var(--accent);"></i> Descubre tu próximo destino
            </h6>
            <span class="sub">Promociones y recomendaciones exclusivas.</span>
        </div>

        {{-- CONTENIDO VITRINA --}}
        <div class="row g-2">
            
            {{-- Tarjeta Principal (Hero) --}}
            <div class="col-12">
                <div class="rsv-hero-card">
                    <i class="bi bi-sun hero-bg-icon"></i>
                    <div class="rsv-hero-title">¡Temporada de Vacaciones!</div>
                    <div class="rsv-hero-text">Reserva anticipadamente y asegura los mejores lugares para tu descanso.</div>
                    <button type="button" class="btn-light-soft shadow-sm" onclick="document.querySelector('[data-bs-target=\'#nr-sub-buscador\']').click()">
                        Ir al Buscador <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- Tarjetas Secundarias --}}
            <div class="col-6">
                <div class="rsv-feature-card">
                    <i class="bi bi-star-fill rsv-feature-icon" style="color:#b08a3e;"></i>
                    <div class="val mb-1">Destacadas</div>
                    <div class="sub">Conoce los alojamientos mejor calificados.</div>
                </div>
            </div>
            
            <div class="col-6">
                <div class="rsv-feature-card">
                    <i class="bi bi-clock-history rsv-feature-icon" style="color:var(--ink-4);"></i>
                    <div class="val mb-1">Vuelve a visitarnos</div>
                    <div class="sub">Revisa tu historial y repite tu experiencia.</div>
                </div>
            </div>
            
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- SUB-TAB 2: BUSCADOR (LLAMADO AL ARCHIVO NIVEL 3)          --}}
    {{-- ========================================================= --}}
    <div class="tab-pane fade" id="nr-sub-buscador" role="tabpanel">
        @if(view()->exists('rsv.cliente.partials.nueva-reserva.buscador'))
            @include('rsv.cliente.partials.nueva-reserva.buscador')
        @else
            <div class="text-center py-4">
                <i class="bi bi-hourglass-split mb-2 d-block" style="font-size:18px; color:var(--ink-4);"></i>
                <span class="val">Buscador (Próximamente)</span>
            </div>
        @endif
    </div>

    {{-- ========================================================= --}}
    {{-- SUB-TAB 3: CHECKOUT (LLAMADO AL ARCHIVO NIVEL 3)          --}}
    {{-- ========================================================= --}}
    <div class="tab-pane fade" id="nr-sub-checkout" role="tabpanel">
        @if(view()->exists('rsv.cliente.partials.nueva-reserva.checkout'))
            @include('rsv.cliente.partials.nueva-reserva.checkout')
        @else
            <div class="text-center py-4">
                <i class="bi bi-hourglass-split mb-2 d-block" style="font-size:18px; color:var(--ink-4);"></i>
                <span class="val">Checkout (Próximamente)</span>
            </div>
        @endif
    </div>
</div>