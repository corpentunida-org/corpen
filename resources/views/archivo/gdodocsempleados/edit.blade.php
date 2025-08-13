<x-base-layout>
    <h4>Editar Documento de Empleado</h4>
    @include('archivo.gdodocsempleados.form', [
        'route' => route('archivo.gdodocsempleados.update', $gdodocsempleado->id),
        'method' => 'PUT',
        'doc' => $gdodocsempleado
    ])

</x-base-layout>
