{{--
|--------------------------------------------------------------------------
| Vista: certificados/ingesta/index.blade.php
|--------------------------------------------------------------------------
| Propósito : Visor y gestor de lotes de ingesta ERP (Staging).
|--------------------------------------------------------------------------
--}}
<x-base-layout>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            integrity="sha256-oVuCFqsKIbRHvGQXDhRaBEJ9oMH2DhJCj2wr7KpBbSA="
            crossorigin="anonymous">
    </script>

    {{-- 1. ESTILOS --}}
    @include('certificados.ingesta.partials._styles')

    <div class="page-wrap py-4" style="min-height:100vh;">
        <div class="container-fluid px-xl-4">
            <div class="row g-4 m-0">

                {{-- COLUMNA PRINCIPAL (CONTENIDO) --}}
                <div class="col-12 col-xl-9">

                    {{-- 2. CABECERA Y BREADCRUMBS --}}
                    @include('certificados.ingesta.partials._header')

                    {{-- 3. ALERTAS Y MODAL DE TERCEROS --}}
                    @include('certificados.ingesta.partials._alerts')

                    {{-- 4. TARJETAS KPI --}}
                    @include('certificados.ingesta.partials._kpis')

                    {{-- 5. PANEL DE CARGA DE ARCHIVOS Y GRÁFICO --}}
                    @include('certificados.ingesta.partials._upload_panel')

                    {{-- 6. BARRA DE ACCIÓN DE INYECCIÓN --}}
                    @include('certificados.ingesta.partials._action_bar')

                    {{-- 7. TABLA DE AUDITORÍA --}}
                    @include('certificados.ingesta.partials._table')

                </div>

                {{-- COLUMNA LATERAL (SIDEBAR FIJO) CON FLIP CARD ANIMADO --}}
                <div class="col-12 col-xl-3">
                    {{-- 8. SIDEBAR PERIODOS --}}
                    @include('certificados.ingesta.partials._sidebar')
                </div>

                {{-- 9. OVERLAY DE CARGA --}}
                @include('certificados.ingesta.partials._loader')

            </div>
        </div>
    </div>
    </div> {{-- 10. MODALES --}}
    @include('certificados.ingesta.partials._modals')

    {{-- 11. SCRIPTS --}}
    @include('certificados.ingesta.partials._scripts')
</x-base-layout>
