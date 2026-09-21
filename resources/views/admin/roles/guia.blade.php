<x-base-layout>
    @section('titlepage', 'Guía de Perfiles y Permisos')

    <style>
        .guia-card { background: #fff; border: 1px solid #eaedf1; border-radius: 16px; box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04); }
        .guia-paso { display: flex; gap: 1rem; margin-bottom: 1rem; }
        .guia-num { flex: 0 0 34px; height: 34px; border-radius: 50%; background: #4f46e5; color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
        .guia-card code { background: #f1f5f9; padding: .1rem .4rem; border-radius: 6px; color: #4338ca; font-size: .85em; }
        .guia-card pre { background: #0f172a; color: #e2e8f0; border-radius: 10px; padding: 1rem; font-size: .82rem; overflow-x: auto; }
    </style>

    <div class="container-fluid px-0 pb-5" style="max-width: 980px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <a href="{{ route('admin.roles.matriz') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-2"></i> Ir a la Matriz de Permisos</a>
        </div>

        <h2 class="fw-bold mb-1"><i class="bi bi-signpost-split me-2 text-primary"></i>Guía paso a paso: perfiles y permisos</h2>
        <p class="text-muted">Cómo se da acceso a las personas en la aplicación, y qué hacer cada vez que se crea o cambia un permiso.</p>

        {{-- 1. Cómo funciona --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">1. Cómo funciona (léelo una vez)</h5>
            <ul class="mb-0">
                <li class="mb-2"><strong>Perfil</strong> = un grupo o área (Cartera, Contabilidad, Asociado…). Es lo único que se le asigna a una persona.</li>
                <li class="mb-2"><strong>Cada usuario tiene UN solo perfil.</strong> Asignar uno nuevo reemplaza el anterior.</li>
                <li class="mb-2"><strong>Los permisos viven en el perfil</strong>, no en la persona. Se marcan en la <a href="{{ route('admin.roles.matriz') }}">Matriz de Permisos</a> y el cambio llega de inmediato a todos los que tienen ese perfil.</li>
                <li class="mb-2"><strong>Permisos <code>menu.*</code></strong> deciden qué módulos aparecen en el menú lateral (ej. <code>menu.cartera</code>, <code>menu.interacciones</code> = Daytrack, <code>menu.certificados</code>).</li>
                <li class="mb-2"><strong>Permisos <code>modulo.recurso.accion</code></strong> deciden qué botones y pantallas puntuales puede usar dentro de un módulo (ej. <code>seguros.poliza.update</code>).</li>
                <li><strong>No hay permisos sueltos por persona.</strong> Si alguien necesita algo distinto, se crea o ajusta un perfil.</li>
            </ul>
        </div>

        {{-- 2. Tareas frecuentes --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">2. Tareas del día a día (para quien administra)</h5>

            <h6 class="fw-bold text-primary mt-2">A. Llegó un empleado nuevo</h6>
            <div class="guia-paso"><div class="guia-num">1</div><div>Crea el usuario en <em>Admin → Usuarios</em> y elige el <strong>perfil de su área</strong>. Listo: ya tiene sus menús y permisos.</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div>Alternativa: crea el usuario, abre su edición y usa <strong>«Copiar Perfil de Otro Usuario»</strong> eligiendo a un compañero de la misma área. La pantalla te muestra qué perfil se asignará antes de confirmar.</div></div>

            <h6 class="fw-bold text-primary mt-4">B. Una persona cambia de área</h6>
            <div class="guia-paso"><div class="guia-num">1</div><div>Abre su usuario → <strong>Perfil de la persona</strong> → elige el perfil nuevo y guarda. Se le quita el anterior y pierde esos menús y permisos automáticamente (te pide confirmar).</div></div>

            <h6 class="fw-bold text-primary mt-4">C. Crear el perfil de un área nueva (ej. «Cartera»)</h6>
            <div class="guia-paso"><div class="guia-num">1</div><div>Entra a la <a href="{{ route('admin.roles.matriz', ['nuevo' => 1]) }}#cardNuevoPerfil">Matriz de Permisos</a> y usa <strong>Crear perfil nuevo</strong> (nombre del área).</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div>Se abre el perfil vacío. Marca primero los <code>menu.*</code> de los módulos que esa área debe ver (Cartera, Daytrack, Maestras, Certificados…).</div></div>
            <div class="guia-paso"><div class="guia-num">3</div><div>Marca los permisos finos de cada módulo (usa el buscador: escribe el módulo, ej. «seguros») y pulsa <strong>Actualizar</strong>.</div></div>
            <div class="guia-paso"><div class="guia-num">4</div><div>Asigna el perfil a las personas del área (ver A y B). Cuando llegue alguien nuevo, solo repites el paso A.</div></div>

            <h6 class="fw-bold text-primary mt-4">D. Dar o quitar un acceso a toda un área</h6>
            <div class="guia-paso"><div class="guia-num">1</div><div>Matriz de Permisos → abre el perfil → marca o desmarca el permiso → <strong>Actualizar</strong>. Se aplica de inmediato a todas las personas de ese perfil.</div></div>

            <h6 class="fw-bold text-primary mt-4">E. Vacaciones, licencia o retiro</h6>
            <div class="guia-paso"><div class="guia-num">1</div><div>No se cambia el perfil: se usa <strong>Bloquear</strong> en el usuario (reversible). Un retiro reportado bloquea la cuenta automáticamente. Una cuenta con historial no se puede eliminar, solo bloquear.</div></div>
        </div>

        {{-- 3. Permiso nuevo --}}
        <div class="guia-card p-4 mb-4 border-warning">
            <h5 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>3. Cada vez que se crea o cambia un permiso (obligatorio)</h5>
            <p class="text-muted fs-13">Sigue estos pasos siempre. Un permiso que no está en ningún perfil no le sirve a nadie.</p>

            <div class="guia-paso"><div class="guia-num">1</div><div><strong>Nómbralo bien:</strong> <code>modulo.recurso.accion</code>, minúsculas, sin espacios ni tildes (ej. <code>cartera.morosos.generarcarta</code>). El sistema rechaza otros formatos y los que empiezan por <code>menu.</code>.</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div><strong>Protégelo en el código:</strong> en la ruta <code>->middleware(['auth', 'candirect:cartera.morosos.generarcarta'])</code> y en la vista <code>@candirect('cartera.morosos.generarcarta') … @endcandirect</code>. Sin esto, el permiso existe pero no controla nada.</div></div>
            <div class="guia-paso"><div class="guia-num">3</div><div><strong>Créalo</strong> en <em>Admin → Roles → Registrar Nuevo Permiso</em>, eligiendo el perfil al que pertenece. Queda asignado a ese perfil y te lleva a la Matriz.</div></div>
            <div class="guia-paso"><div class="guia-num">4</div><div><strong>Asígnalo en la Matriz</strong> a los <em>demás</em> perfiles que lo necesiten (y desmárcalo donde no corresponda).</div></div>
            <div class="guia-paso"><div class="guia-num">5</div><div><strong>Verifica</strong> en el servidor: <pre class="mb-0 mt-2">php artisan permisos:auditar</pre>No debe reportar permisos sin perfil, módulos de menú sin permiso, ni usuarios con más de un perfil.</div></div>
            <div class="guia-paso"><div class="guia-num">6</div><div><strong>Prueba</strong> entrando con un usuario del perfil y con uno que no debería tener el acceso.</div></div>
            <div class="guia-paso"><div class="guia-num">7</div><div><strong>Despliega</strong> (en producción, en este orden): <pre class="mb-0 mt-2">git pull
php artisan migrate --force        # si el cambio incluye migraciones
php artisan optimize:clear
php artisan permisos:auditar
chown -R www-data:www-data storage bootstrap/cache</pre></div></div>

            <div class="alert alert-info fs-13 mb-0 mt-3">
                Cuando el permiso lo crea un desarrollador en una <strong>migración</strong> (recomendado para nuevos módulos): inserta el permiso, asígnalo a los perfiles con <code>role_has_permissions</code> y termina llamando a
                <code>app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos()</code>. Mira <code>database/migrations/2026_09_21_180000_crear_permisos_de_menu.php</code> como ejemplo.
            </div>
        </div>

        {{-- 4. Módulo nuevo --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">4. Cuando se agrega un módulo nuevo al menú lateral</h5>
            <div class="guia-paso"><div class="guia-num">1</div><div>Crea el archivo del menú: <code>resources/views/layouts/actions/&lt;modulo&gt;.blade.php</code> (copia uno existente, ej. <code>cartera.blade.php</code>).</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div>Genera su permiso de menú: <pre class="mb-0 mt-2">php artisan permisos:auditar --crear-menus</pre>Crea <code>menu.&lt;modulo&gt;</code> (no se asigna a nadie todavía).</div></div>
            <div class="guia-paso"><div class="guia-num">3</div><div>En la Matriz marca <code>menu.&lt;modulo&gt;</code> en los perfiles que deben ver el módulo. Si no lo marcas, nadie lo verá.</div></div>
            <div class="guia-paso"><div class="guia-num">4</div><div>Define los permisos finos del módulo siguiendo la sección 3.</div></div>
        </div>

        {{-- 5. Errores frecuentes --}}
        <div class="guia-card p-4">
            <h5 class="fw-bold mb-3">5. Errores frecuentes</h5>
            <ul class="mb-0">
                <li class="mb-2"><strong>«El menú de un módulo no aparece».</strong> Falta marcar <code>menu.&lt;modulo&gt;</code> en el perfil de la persona (Matriz).</li>
                <li class="mb-2"><strong>«Ve el menú pero le sale “no autorizado” en un botón/pantalla».</strong> Falta el permiso fino <code>modulo.recurso.accion</code> en su perfil.</li>
                <li class="mb-2"><strong>«Cambié el perfil pero sigue viendo lo anterior».</strong> Que cierre y abra sesión, o ejecuta <code>php artisan optimize:clear</code> en el servidor.</li>
                <li class="mb-2"><strong>El menú no es seguridad.</strong> Ocultar un menú no protege la ruta. Los módulos que aún no usan <code>candirect</code> en sus rutas siguen accesibles escribiendo la dirección; protégelos con la sección 3.</li>
                <li><strong>Un asociado NO debe recibir permisos de personal</strong> (<code>exequial.*</code>, <code>seguros.*</code>…): muestran datos de todos. Para el autoservicio del asociado se crean permisos propios (ej. <code>asociado.exequiales.ver</code>) que solo consultan sus propios datos.</li>
            </ul>
        </div>
    </div>
</x-base-layout>
