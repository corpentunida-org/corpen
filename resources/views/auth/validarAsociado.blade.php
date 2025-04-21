<x-guest-layout>


    <x-validation-errors class="mb-4" />
    <p style="font-size: 20px;
                font-weight: bold;
                color: #254684;
                text-align: center; padding-top: 100px">Registro Asociado Corpentunida</p>
    <p style="font-size: 16px;
                font-weight: bold;
                color: #254684;
                text-align: center;
                padding-bottom: 15px;">Validar identidad</p>

    <form method="POST" class="w-100 mt-4 pt-2" action="{{ route('validar.asociado') }}" style="padding-bottom: 220px">
        @csrf
        <input type="hidden" name="aux" value="asociado">
        <div>
            <x-label for="nid" value="{{ __('Cédula') }}" />
            <x-input id="nid" class="block mt-1 w-full" type="text" name="nid" :value="old('nid')" required autofocus autocomplete="nid" />
        </div>

        <div class="mt-4">
            <x-label for="fecha" value="{{ __('Fecha nacimiento') }}" />
            <x-input id="fecha" class="block mt-1 w-full" type="text" name="fecha" :value="old('fecha')" required autocomplete="fecha" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-button class="ms-4">
                {{ __('Validar') }}
            </x-button>
        </div>
    </form>
    @push('scripts')
        <script src="{{asset('assets/vendors/js/datepicker.min.js')}}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Selecciona el input por ID y agrega el datepicker
                var dateInput = document.getElementById('fecha');
                if (dateInput) {
                    new Datepicker(dateInput, {
                        // Opciones para el selector de fecha
                        format: 'yyyy-mm-dd', // Formato de la fecha
                        autohide: true, // Ocultar automáticamente al seleccionar
                        todayBtn: true, // Botón para seleccionar hoy
                        clearBtn: true, // Botón para limpiar
                    });
                }
            });
        </script>
    @endpush
</x-guest-layout>
