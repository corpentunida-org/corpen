<x-base-layout>
    <div class="card">
        <!-- Esta es una tarjeta de Bootstrap que sirve para contener el formulario de edición. -->
        
        <div class="card-body">
            <!-- El "cuerpo" de la tarjeta, donde se colocan los contenidos internos como títulos y formularios. -->

            <h5 class="fw-bold mb-4">Editar Tipo de Documento</h5>
            <!-- Título del formulario con negrita (fw-bold) y margen inferior (mb-4). -->

            <form action="{{ route('archivo.gdotipodocumento.update', $tipoDocumento->id) }}" method="POST">
                <!-- Formulario HTML que enviará los datos al servidor. 
                    - 'action' indica la ruta donde se enviarán los datos para actualizar un tipo de documento.
                    - Se usa 'route' de Laravel para generar la URL automáticamente, pasando el ID del tipo de documento.
                    - 'method="POST"' porque HTML no soporta PUT directamente; Laravel interpreta el método real con @method('PUT'). -->

                @csrf
                <!-- Token CSRF de Laravel para proteger el formulario contra ataques de tipo CSRF. Es obligatorio en formularios POST. -->

                @method('PUT')
                <!-- Indica a Laravel que el método HTTP real será PUT (usado para actualizar registros). -->

                @include('archivo.gdotipodocumento.form')
                <!-- Incluye otro archivo Blade llamado 'form.blade.php' que contiene los campos del formulario 
                    (por ejemplo: nombre del tipo de documento, descripción, etc.). Esto evita repetir código. -->

                <div class="mt-4">
                    <!-- Contenedor para los botones con margen superior (mt-4). -->

                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <!-- Botón que envía el formulario al servidor para guardar los cambios. -->

                    <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-secondary">Cancelar</a>
                    <!-- Botón que redirige al usuario de vuelta al listado de tipos de documento sin guardar cambios. -->
                </div>
            </form>
        </div>
    </div>

</x-base-layout>
