<script>
    // Reutiliza el mismo endpoint que usa el formulario de Congregaciones para el pastor
    // (App\Http\Controllers\Maestras\MaeCongregacionController::buscarPastor) — busca por
    // cédula en MaeTerceros, no es específico de "pastor".
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.cedula-cargo').forEach(function (input) {
            const nombreInput = document.getElementById(input.dataset.nombreTarget);

            function buscar(cedula) {
                if (cedula.length >= 5) {
                    nombreInput.value = 'Buscando...';
                    fetch(`{{ route('maestras.buscar.pastor') }}?cedula=${cedula}`)
                        .then(res => {
                            if (!res.ok) throw new Error();
                            return res.json();
                        })
                        .then(data => {
                            nombreInput.value = data.nombre;
                        })
                        .catch(() => {
                            nombreInput.value = 'No encontrado';
                        });
                } else {
                    nombreInput.value = '';
                }
            }

            input.addEventListener('input', () => buscar(input.value));
            input.addEventListener('paste', () => setTimeout(() => buscar(input.value), 10));

            if (input.value.length >= 5) {
                buscar(input.value);
            }
        });
    });
</script>
