<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Crear Tipo de Documento</h5>
            <form action="{{ route('archivo.gdotipodocumento.store') }}" method="POST">
                @csrf
                @include('archivo.gdotipodocumento.form')
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>
