<x-guest-layout>
    <h2 class="fs-20 fw-bolder mb-4">APP Corpentunida</h2>
    <div style="color: red">
        <x-validation-errors class="mb-4" />
    </div>
    <h4 class="fs-13 fw-bold mb-2">Ingresa con tu cuenta</h4>
    <!-- <p class="fs-12 fw-medium text-muted">Thank you for get back <strong>Nelel</strong> web applications, let's access our the best recommendation for you.</p> -->
    <form method="POST" class="w-100 mt-4 pt-2" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <input type="email" name="email" class="form-control" placeholder="Email" value="{{old('email')}}" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password"  class="form-control" placeholder="Contraseña" required>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="rememberMe">
                    <label class="custom-control-label c-pointer" for="rememberMe">Recuerdame</label>
                </div>
            </div>
            <div>
                <a href="{{ route('password.request') }}" class="fs-11 text-primary">{{ __('¿Olvidaste la contraseña?') }}</a>
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Ingresar</button>
        </div>
    </form>
</x-guest-layout>
