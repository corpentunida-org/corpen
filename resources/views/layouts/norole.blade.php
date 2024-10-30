<x-guest-layout>
    <h2 class="fw-bolder mb-4" style="font-size: 120px">4<span class="text-danger">0</span>3</h2>
    <h4 class="fw-bold mb-2">Sin Autorización</h4>
    <p class="fs-12 fw-medium text-muted">Su usuario no tiene rol asignado, por favor comunicarse con el administrador del sistema.</p>

    <form method="POST" action="{{ route('logout') }}" class="mt-5">
        @csrf

        <button type="submit" class="btn btn-light-brand w-100">
            {{ __('Log Out') }}
        </button>
    </form>
</x-guest-layout>
