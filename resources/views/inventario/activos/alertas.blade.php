<x-base-layout>
    <style>
        .wrap { max-width: 1200px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        .alert-card { background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
        .icon-alert { color: #dc2626; font-size: 1.5rem; }
        .text-alert h3 { margin: 0; color: #991b1b; font-size: 1rem; }
        .text-alert p { margin: 2px 0 0 0; color: #b91c1c; font-size: 0.9rem; }
        
        .main-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; text-align: center; color: #64748b; }
    </style>

    <div class="wrap">
        <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 30px;">Alertas de Garantía</h1>

        <div class="alert-card">
            <i class="bi bi-exclamation-triangle-fill icon-alert"></i>
            <div class="text-alert">
                <h3>Garantías por Vencer</h3>
                <p>Equipos cuya garantía finaliza en los próximos 30 días.</p>
            </div>
        </div>

        <div class="main-card">
            <i class="bi bi-check-circle" style="font-size: 3rem; color: #10b981;"></i>
            <h3>Todo en orden</h3>
            <p>No hay equipos próximos a vencer garantía en este momento.</p>
            {{-- Aquí iría un @foreach similar al index si hubiera datos --}}
        </div>
    </div>
</x-base-layout>