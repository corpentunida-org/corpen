<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mensajes = {
            crear: @json($crear ?? '¿Está seguro de crear este registro?'),
            ver: @json($ver ?? '¿Desea ver los detalles?'),
            editar: @json($editar ?? '¿Desea editar este registro?'),
            eliminar: @json($eliminar ?? '¿Desea eliminar este registro? Esta acción no se puede deshacer.')
        };

        document.querySelectorAll('.btnEliminar').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm(mensajes.eliminar)) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.btnCrear').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm(mensajes.crear)) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.btnEditar').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm(mensajes.editar)) {
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.btnVer').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm(mensajes.ver)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
