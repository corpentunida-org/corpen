<x-base-layout>
    @php
        // Antes esta vista nunca la definía: interactions/form.blade.php la usa en más de 10
        // sitios (a qué ruta apunta su propio <form> interno, el título "Editar Interacción #X",
        // el botón "Ver Detalles", el archivo ya guardado...) y todos quedaban en falso por
        // variable indefinida — el <form> interno terminaba apuntando a crear una interacción
        // nueva en vez de actualizar esta. Mismo criterio que ya usa create.blade.php.
        $modoEdicion = true;
    @endphp
    <div class="card">
        {{-- interactions/form.blade.php ya trae su propio <form> completo (acción, método,
             csrf, spoofing de PUT si $modoEdicion) — envolverlo en otro <form> aquí (como estaba
             antes) genera HTML inválido (formularios anidados) y el navegador decide por su
             cuenta cuál de los dos "gana" al enviar. Mismo criterio que create.blade.php, que ya
             dejaba su <form> propio comentado por esta misma razón. --}}
        @include('interactions.form')
    </div>
</x-base-layout>
