<x-guest-layout>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('¿Olvidaste tu contraseña? No hay problema. Solo ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña, que te permitirá elegir una nueva.') }}
        </div>


    <div style="color: red">
        <x-validation-errors class="mb-4" />
    </div>

        <form method="POST" class="w-100 mt-4 pt-2" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <input type="email" name="email" class="form-control" placeholder="Email" value="{{old('email')}}" required>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Recibir enlace para restablecer la contraseña') }}
                </x-button>
            </div>
        </form>

</x-guest-layout>
