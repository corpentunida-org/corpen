<x-guest-layout>
    <p style="font-size: 18px; font-weight: bold; color: #254684; text-align: center; padding-bottom: 10px;">
        Debes cambiar tu contraseña
    </p>
    <p style="text-align: center; color: #6b7280; font-size: 14px; padding-bottom: 15px;">
        Un administrador te asignó una contraseña temporal. Antes de continuar, elige una nueva
        contraseña solo tú conozcas.
    </p>

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('password.forzar.store') }}">
        @csrf

        <div>
            <x-label for="password" value="{{ __('Nueva contraseña') }}" />
            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autofocus autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button>
                {{ __('Guardar y continuar') }}
            </x-button>
        </div>
    </form>
</x-guest-layout>
