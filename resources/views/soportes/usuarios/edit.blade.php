<x-base-layout>
    @section('titlepage', 'Editar Usuario')

    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('soportes.usuarios.update', $usuario->id) }}">
                    @csrf
                    @method('PUT')
                    @include('soportes.usuarios.form')
                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-warning" type="submit"><i class="feather-save me-2"></i> Guardar Cambios</button>
                        <a href="{{ route('soportes.usuarios.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
