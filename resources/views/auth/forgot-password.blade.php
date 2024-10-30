<x-guest-layout>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>


    <div style="color: red">
        <x-validation-errors class="mb-4" />
    </div>

        <form method="POST" class="w-100 mt-4 pt-2" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="{{ __('Email') }}" />
                <input type="email" name="email" class="form-control" placeholder="Email or Username" value="{{old('email')}}" required>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>

</x-guest-layout>
