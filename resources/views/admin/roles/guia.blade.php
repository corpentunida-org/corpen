<x-base-layout>
    @section('titlepage', 'Guía: crear un perfil nuevo')

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

        <h2 class="fw-bold mb-1"><i class="bi bi-signpost-split me-2 text-primary"></i>Guía paso a paso: crear un perfil nuevo</h2>
        <p class="text-muted">Cómo dar acceso a un equipo de trabajo nuevo (o a una persona con un cargo nuevo) sin configurarlo persona por persona.</p>

        {{-- 1. Cómo funciona --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">1. Cómo está organizado (léelo una vez)</h5>
            <ul class="mb-0">
                <li class="mb-2"><strong>Área</strong> = un equipo (Cartera, Asociado, Seguros…). Solo agrupa perfiles para encontrarlos fácil.</li>
                <li class="mb-2"><strong>Perfil</strong> = el paquete de accesos (menús y permisos) de un cargo dentro del área (ej. <em>cartera</em> y <em>carteraadmon</em> en el área CARTERA). Es lo único que se le asigna a una persona.</li>
                <li class="mb-2"><strong>Cada usuario tiene UN solo perfil.</strong> Asignar uno nuevo reemplaza el anterior.</li>
                <li class="mb-2"><strong>Los permisos viven en el perfil, no en la persona.</strong> Al cambiarlos en la <a href="{{ route('admin.roles.matriz') }}">Matriz de Permisos</a>, el cambio llega de inmediato a todos los que tienen ese perfil.</li>
                <li><strong>Dos tipos de permisos:</strong> los <code>menu.*</code> deciden qué módulos aparecen en el menú lateral (ej. <code>menu.cartera</code>, <code>menu.interacciones</code> = Daytrack), y los <code>modulo.recurso.accion</code> deciden qué botones y pantallas puede usar dentro de cada módulo.</li>
            </ul>
        </div>

        {{-- 2. Crear perfil nuevo --}}
        <div class="guia-card p-4 mb-4 border-primary">
            <h5 class="fw-bold mb-1"><i class="bi bi-plus-circle text-primary me-2"></i>2. Crear un perfil nuevo</h5>
            <p class="text-muted fs-13">Ejemplo: el área de Cartera necesita un perfil <em>carteraadmon</em> con Daytrack, Maestras y Certificados.</p>

            <div class="guia-paso"><div class="guia-num">1</div><div>Entra a la <a href="{{ route('admin.roles.matriz') }}">Matriz de Permisos</a>. Busca el <strong>área</strong> con el buscador de arriba (ej. «cartera»).</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div>Si el área <strong>no existe</strong>, créala con <strong>Crear área nueva</strong>. Queda vacía, lista para recibir perfiles.</div></div>
            <div class="guia-paso"><div class="guia-num">3</div><div>Despliega el área y usa <strong>«Agregar un perfil a esta área»</strong> (o la tarjeta <strong>Crear perfil nuevo</strong> eligiendo el área). Escribe el nombre del perfil, por ejemplo <code>carteraadmon</code>. Nace sin permisos.</div></div>
            <div class="guia-paso"><div class="guia-num">4</div><div>Abre el perfil nuevo y marca primero los <code>menu.*</code> de los módulos que debe ver: <code>menu.cartera</code>, <code>menu.interacciones</code> (Daytrack), <code>menu.maestras</code>, <code>menu.certificados</code>…</div></div>
            <div class="guia-paso"><div class="guia-num">5</div><div>Marca los permisos de cada módulo (usa el buscador del perfil: escribe el módulo, ej. «seguros»). Pulsa <strong>Actualizar</strong>. Tip: para copiar lo de otro perfil, ábrelo en otra pestaña y marca lo mismo.</div></div>
            <div class="guia-paso"><div class="guia-num">6</div><div><strong>Asigna el perfil a las personas.</strong> En <em>Admin → Usuarios →</em> la persona → <strong>Perfil de la persona</strong>, elige el perfil nuevo y guarda. Para el siguiente compañero del área basta usar <strong>«Copiar Perfil de Otro Usuario»</strong> con alguien que ya lo tiene.</div></div>
            <div class="guia-paso"><div class="guia-num">7</div><div><strong>Prueba:</strong> entra con «Ver como» (asociados) o pídele a la persona que inicie sesión y confirme que ve sus menús y puede usar sus pantallas.</div></div>
        </div>

        {{-- 3. Otras tareas --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">3. Otras tareas del día a día</h5>
            <ul class="mb-0">
                <li class="mb-2"><strong>Llegó un empleado nuevo:</strong> créalo en <em>Admin → Usuarios</em> con el perfil de su cargo, o cópiale el perfil de un compañero.</li>
                <li class="mb-2"><strong>Cambió de área o cargo:</strong> en su usuario elige el perfil nuevo. Reemplaza el anterior (te pide confirmar) y pierde esos menús y permisos.</li>
                <li class="mb-2"><strong>Dar o quitar un acceso a todo un equipo:</strong> Matriz → abre el perfil → marca o desmarca el permiso → <strong>Actualizar</strong>.</li>
                <li class="mb-2"><strong>Cambiar el nombre de un perfil o de un área:</strong> al desplegarlos tienen su campo de nombre y <strong>Guardar nombre</strong>. <strong>Mover un perfil a otra área:</strong> campo «Área de este perfil».</li>
                <li class="mb-2"><strong>Eliminar un perfil:</strong> solo si <em>ningún usuario</em> lo tiene asignado (el botón se bloquea y dice cuántos lo tienen). Antes asígnales otro perfil. El perfil Asociado no se puede eliminar. <strong>Eliminar un área:</strong> solo si está vacía.</li>
                <li><strong>Vacaciones, licencia o retiro:</strong> no se cambia el perfil: se usa <strong>Bloquear</strong> en el usuario (reversible). Un retiro reportado bloquea la cuenta solo. Una cuenta con historial no se puede eliminar, solo bloquear.</li>
            </ul>
        </div>

        {{-- 4. Errores frecuentes --}}
        <div class="guia-card p-4 mb-4">
            <h5 class="fw-bold mb-3">4. Errores frecuentes</h5>
            <ul class="mb-0">
                <li class="mb-2"><strong>«El menú de un módulo no aparece».</strong> Falta marcar <code>menu.&lt;módulo&gt;</code> en el perfil de la persona.</li>
                <li class="mb-2"><strong>«Ve el menú pero un botón o pantalla dice no autorizado».</strong> Falta el permiso <code>modulo.recurso.accion</code> en su perfil.</li>
                <li class="mb-2"><strong>«Cambié el perfil pero sigue viendo lo anterior».</strong> Que cierre sesión y vuelva a entrar.</li>
                <li class="mb-2"><strong>El menú no es seguridad.</strong> Ocultar un menú no protege la pantalla. Los módulos que aún no exigen permiso en sus rutas siguen accesibles escribiendo la dirección (ver la sección 5).</li>
                <li><strong>Un asociado NO debe recibir permisos de personal</strong> (<code>exequial.*</code>, <code>seguros.*</code>…): muestran datos de todos. Para el autoservicio del asociado se crean permisos propios (ej. <code>asociado.exequiales.ver</code>) que solo consultan sus propios datos.</li>
            </ul>
        </div>

        {{-- 5. Para desarrolladores --}}
        <div class="guia-card p-4 border-warning">
            <h5 class="fw-bold mb-1"><i class="bi bi-code-slash text-warning me-2"></i>5. Solo para desarrollo: cuando se agrega un permiso o un módulo</h5>
            <p class="text-muted fs-13">Los permisos nacen junto con el código que los usa (por eso no se crean desde la pantalla: un permiso que ninguna ruta revisa no controla nada).</p>

            <div class="guia-paso"><div class="guia-num">1</div><div><strong>Nómbralo bien:</strong> <code>modulo.recurso.accion</code> en minúsculas, sin espacios ni tildes (ej. <code>cartera.morosos.generarcarta</code>).</div></div>
            <div class="guia-paso"><div class="guia-num">2</div><div><strong>Protégelo en el código:</strong> en la ruta <code>->middleware(['auth', 'candirect:cartera.morosos.generarcarta'])</code> y en la vista <code>@candirect('cartera.morosos.generarcarta') … @endcandirect</code>.</div></div>
            <div class="guia-paso"><div class="guia-num">3</div><div><strong>Créalo en una migración</strong> y asígnalo a los perfiles que corresponda (<code>role_has_permissions</code>); termina con <code>app(\App\Services\Admin\PermisosPorRolService::class)->sincronizarTodos()</code>. Ejemplo: <code>database/migrations/2026_09_21_180000_crear_permisos_de_menu.php</code>.</div></div>
            <div class="guia-paso"><div class="guia-num">4</div><div><strong>Módulo nuevo en el menú:</strong> crea <code>resources/views/layouts/actions/&lt;modulo&gt;.blade.php</code>, ejecuta <pre class="mb-0 mt-2">php artisan permisos:auditar --crear-menus</pre>y marca <code>menu.&lt;modulo&gt;</code> en los perfiles que lo verán (Matriz).</div></div>
            <div class="guia-paso"><div class="guia-num">5</div><div><strong>Verifica:</strong> <pre class="mb-0 mt-2">php artisan permisos:auditar</pre>No debe reportar permisos sin perfil, módulos de menú sin permiso ni usuarios desfasados.</div></div>
            <div class="guia-paso"><div class="guia-num">6</div><div><strong>Despliega:</strong> haz merge a <code>main</code> (el despliegue es automático). Si hay migraciones, aplícalas en la base compartida <em>antes</em> de que el código nuevo las necesite.</div></div>
        </div>
    </div>
</x-base-layout>
