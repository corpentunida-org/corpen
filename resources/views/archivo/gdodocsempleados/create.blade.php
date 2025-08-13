<x-base-layout>
    <h4>Nuevo Documento de Empleado</h4>
    @include('archivo.gdodocsempleados.form', [
        'route' => route('archivo.gdodocsempleados.store'),
        'method' => 'POST',
        'doc' => null
    ])
</x-base-layout>
