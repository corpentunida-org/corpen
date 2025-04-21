<x-guest-layout>


    <x-validation-errors class="mb-4" />
    <p style="font-size: 20px;
                font-weight: bold;
                color: #254684;
                text-align: center;
                padding-bottom: 15px;">Registro Asociado Corpentunida</p>

    <form method="POST" class="w-100 mt-4 pt-2" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="aux" value="asociado">
        <div>
            <x-label for="nid" value="{{ __('Cédula') }}" />
            <x-input id="nid" class="block mt-1 w-full" type="text" name="nid" :value="$nid" required  autocomplete="nid" readonly />
        </div>

        <div class="mt-4">
            <x-label for="fecha" value="{{ __('Fecha nacimiento') }}" />
            <x-input id="fecha" class="block mt-1 w-full" type="text" name="fecha" :value="substr($birthdate, 0, 10)" required autocomplete="fecha"  readonly/>
        </div>

        <div class="mt-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$asociadoArray['original']['data']['name']" required autocomplete="name" readonly/>
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mt-4">
                <x-label for="terms">
                    <div class="flex items-center">
                        <x-checkbox name="terms" id="terms" required />

                        <div class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-button class="ms-4">
                {{ __('Register') }}
            </x-button>
        </div>
    </form>

</x-guest-layout>
