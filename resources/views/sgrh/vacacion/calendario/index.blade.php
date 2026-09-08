<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Calendario de vacaciones</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                <span class="badge bg-success-subtle text-success ms-1">Aprobada</span>
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div id="calendarGlobal"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendarGlobal');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'es',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    events: "{{ route('sgrh.vacacion.calendario.eventos') }}",
                    eventClick: function (info) {
                        window.location.href = "{{ url('sgrh/vacaciones/solicitudes') }}/" + info.event.extendedProps.id;
                    },
                });
                calendar.render();
            });
        </script>
    @endpush
</x-base-layout>
