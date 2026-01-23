<x-base-layout>
    <style>
        /* LAYOUT GENERAL */
        .layout-grid { display: grid; grid-template-columns: 400px 1fr; gap: 30px; max-width: 1300px; margin: 30px auto; font-family: 'Inter', sans-serif; color: #0f172a; }
        @media (max-width: 1000px) { .layout-grid { grid-template-columns: 1fr; } }
        
        .card-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; height: fit-content; }
        .sticky-col { position: sticky; top: 20px; }

        /* TABS (Pestañas) */
        .tabs-head { display: flex; background: #f8fafc; border-bottom: 1px solid #e2e8f0; overflow-x: auto; }
        .tab-link { flex: 1; padding: 15px 10px; border: none; background: none; font-size: 0.8rem; font-weight: 700; color: #64748b; cursor: pointer; border-bottom: 3px solid transparent; white-space: nowrap; transition: 0.2s; text-align: center; }
        .tab-link:hover { color: #0f172a; background: #f1f5f9; }
        .tab-link.active { color: #4f46e5; border-bottom-color: #4f46e5; background: #fff; }
        
        .tab-body { padding: 25px; display: none; animation: fadeIn 0.3s ease; }
        .tab-body.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* FORMULARIOS */
        .inp { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #fbfcfd; font-size: 0.9rem; margin-bottom: 15px; transition: 0.2s; }
        .inp:focus { outline: none; border-color: #4f46e5; background: #fff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .lbl { display: block; font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.05em; }
        
        .btn-submit { width: 100%; background: #0f172a; color: #fff; padding: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; transition: 0.2s; }
        .btn-submit:hover { background: #1e293b; }
        .btn-cancel { width: 100%; background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; margin-top: 10px; display: none; }

        /* MINI SECCIONES (Catálogos Base) */
        .mini-section { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; transition: 0.3s; opacity: 0.8; }
        .mini-section:hover { opacity: 1; border-color: #cbd5e1; }
        .mini-section.editing { border: 2px solid #4f46e5; background: #eef2ff; opacity: 1; transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.15); }
        .mini-head { font-weight: 800; font-size: 0.8rem; margin-bottom: 10px; color: #334155; display: flex; justify-content: space-between; align-items: center; }
        .mode-badge { font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; background: #f1f5f9; color: #64748b; text-transform: uppercase; }
        .editing .mode-badge { background: #4f46e5; color: #fff; }

        /* TABLAS DE AUDITORÍA */
        .t-audit { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .t-audit th { text-align: left; padding: 12px 15px; color: #64748b; font-size: 0.7rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; font-weight: 700; background: #f8fafc; }
        .t-audit td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        .t-audit tr:last-child td { border-bottom: none; }
        .t-audit tr:hover td { background: #f8fafc; }

        .tag { padding: 3px 8px; background: #e0e7ff; color: #3730a3; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .id-col { color: #94a3b8; font-family: monospace; font-size: 0.75rem; }

        .act-btn { border: none; background: none; cursor: pointer; font-size: 1rem; margin-left: 8px; transition: 0.2s; color: #94a3b8; }
        .act-btn.edit:hover { color: #2563eb; transform: scale(1.1); } 
        .act-btn.del:hover { color: #dc2626; transform: scale(1.1); }
    </style>

    <div class="layout-grid">
        
        {{-- COLUMNA IZQUIERDA: FORMULARIOS (STICKY) --}}
        <div class="card-box sticky-col">
            <div class="tabs-head">
                <button class="tab-link active" onclick="switchTab('left', 'form-main')" id="btn-left-main">Vincular (Subgrupo)</button>
                <button class="tab-link" onclick="switchTab('left', 'form-cats')" id="btn-left-cats">Catálogos Base</button>
            </div>

            {{-- 1. FORMULARIO PRINCIPAL (SUBGRUPOS) --}}
            <div id="form-main" class="tab-body left-tab active">
                <h3 style="margin:0 0 15px 0; font-size:1.1rem; color:#0f172a;" id="title-main">Nueva Vinculación</h3>
                <p style="font-size:0.8rem; color:#64748b; margin-bottom:20px;">Use esto para crear la categoría final (Ej: "Portátiles") uniendo los catálogos.</p>

                <form id="formMain" action="{{ route('inventario.clasificacion.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodMain" value="POST">
                    
                    <label class="lbl">Nombre Subgrupo</label>
                    <input type="text" name="nombre" id="inpMainName" class="inp" required placeholder="Ej: Portátiles...">

                    <label class="lbl">Tipo (Raíz)</label>
                    <select name="id_InvTipos" id="selMainTipo" class="inp" required>
                        <option value="">Seleccione...</option>
                        @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                    </select>

                    <label class="lbl">Línea</label>
                    <select name="id_InvLineas" id="selMainLinea" class="inp" required>
                        <option value="">Seleccione...</option>
                        @foreach($lineas as $l) <option value="{{ $l->id }}">{{ $l->nombre }}</option> @endforeach
                    </select>

                    <label class="lbl">Grupo</label>
                    <select name="id_InvGrupos" id="selMainGrupo" class="inp" required>
                        <option value="">Seleccione...</option>
                        @foreach($grupos as $g) <option value="{{ $g->id }}">{{ $g->nombre }}</option> @endforeach
                    </select>

                    <button type="submit" class="btn-submit" id="btnMain">Guardar Vinculación</button>
                    <button type="button" onclick="resetMain()" class="btn-cancel" id="cancelMain">Cancelar Edición</button>
                </form>
            </div>

            {{-- 2. FORMULARIOS CATÁLOGOS BASE --}}
            <div id="form-cats" class="tab-body left-tab">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; font-size:1.1rem;">Catálogos Base</h3>
                    <button type="button" onclick="resetCats()" style="font-size:0.75rem; border:none; background:none; color:#64748b; cursor:pointer; text-decoration:underline;">Resetear Todo</button>
                </div>

                {{-- Form TIPO --}}
                <div class="mini-section" id="box-tipo">
                    <div class="mini-head"><span><i class="bi bi-tag-fill"></i> 1. TIPOS</span> <span class="mode-badge" id="lbl-tipo">NUEVO</span></div>
                    <form id="formTipo" action="{{ route('inventario.clasificacion.tipo.store') }}" method="POST">
                        @csrf <input type="hidden" name="_method" id="methodTipo" value="POST">
                        <div style="display:flex; gap:10px;">
                            <input type="text" name="nombre" id="inpTipoName" class="inp" placeholder="Ej: Tangible" style="margin-bottom:0;" required>
                            <button type="submit" class="btn-submit" style="width:auto; padding:0 20px;"><i class="bi bi-check-lg"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Form LÍNEA --}}
                <div class="mini-section" id="box-linea">
                    <div class="mini-head"><span><i class="bi bi-list-nested"></i> 2. LÍNEAS</span> <span class="mode-badge" id="lbl-linea">NUEVO</span></div>
                    <form id="formLinea" action="{{ route('inventario.clasificacion.linea.store') }}" method="POST">
                        @csrf <input type="hidden" name="_method" id="methodLinea" value="POST">
                        <select name="id_InvTipos" id="inpLineaParent" class="inp" style="padding:8px; margin-bottom:10px;" required>
                            <option value="">Pertenece a Tipo...</option>
                            @foreach($tipos as $t) <option value="{{ $t->id }}">{{ $t->nombre }}</option> @endforeach
                        </select>
                        <div style="display:flex; gap:10px;">
                            <input type="text" name="nombre" id="inpLineaName" class="inp" placeholder="Ej: Hardware" style="margin-bottom:0;" required>
                            <button type="submit" class="btn-submit" style="width:auto; padding:0 20px;"><i class="bi bi-check-lg"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Form GRUPO --}}
                <div class="mini-section" id="box-grupo">
                    <div class="mini-head"><span><i class="bi bi-collection-fill"></i> 3. GRUPOS</span> <span class="mode-badge" id="lbl-grupo">NUEVO</span></div>
                    <form id="formGrupo" action="{{ route('inventario.clasificacion.grupo.store') }}" method="POST">
                        @csrf <input type="hidden" name="_method" id="methodGrupo" value="POST">
                        <select name="id_InvLineas" id="inpGrupoParent" class="inp" style="padding:8px; margin-bottom:10px;" required>
                            <option value="">Pertenece a Línea...</option>
                            @foreach($lineas as $l) <option value="{{ $l->id }}">{{ $l->nombre }}</option> @endforeach
                        </select>
                        <div style="display:flex; gap:10px;">
                            <input type="text" name="nombre" id="inpGrupoName" class="inp" placeholder="Ej: Computadores" style="margin-bottom:0;" required>
                            <button type="submit" class="btn-submit" style="width:auto; padding:0 20px;"><i class="bi bi-check-lg"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: LISTADOS DE AUDITORÍA --}}
        <div class="card-box">
            <div class="tabs-head">
                <button class="tab-link active" onclick="switchTab('right', 'list-sub')">Subgrupos (Final)</button>
                <button class="tab-link" onclick="switchTab('right', 'list-grupos')">Grupos</button>
                <button class="tab-link" onclick="switchTab('right', 'list-lineas')">Líneas</button>
                <button class="tab-link" onclick="switchTab('right', 'list-tipos')">Tipos</button>
            </div>

            {{-- 1. TABLA SUBGRUPOS --}}
            <div id="list-sub" class="tab-body right-tab active">
                <table class="t-audit">
                    <thead><tr><th>Subgrupo</th><th>Tipo</th><th>Línea</th><th>Grupo</th><th style="text-align:right">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($subgrupos as $s)
                        <tr>
                            <td style="font-weight:700">{{ $s->nombre }}</td>
                            <td>{{ $s->tipo->nombre ?? '-' }}</td>
                            <td>{{ $s->linea->nombre ?? '-' }}</td>
                            <td>{{ $s->grupo->nombre ?? '-' }}</td>
                            <td style="text-align:right">
                                <button class="act-btn edit" onclick='editMain(@json($s))'><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('inventario.clasificacion.destroy', $s->id) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="act-btn del"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding:20px">{{ $subgrupos->links() }}</div>
            </div>

            {{-- 2. TABLA GRUPOS --}}
            <div id="list-grupos" class="tab-body right-tab">
                <table class="t-audit">
                    <thead><tr><th>ID</th><th>Grupo</th><th>Pertenece a Línea</th><th style="text-align:right">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($grupos_list as $g)
                        <tr>
                            <td class="id-col">#{{ $g->id }}</td>
                            <td style="font-weight:600">{{ $g->nombre }}</td>
                            <td><span class="tag">{{ $g->linea->nombre ?? 'Huérfano' }}</span></td>
                            <td style="text-align:right">
                                <button class="act-btn edit" onclick='editParam("grupo", @json($g))'><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $g->id, 'tipo' => 'grupo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="act-btn del"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. TABLA LÍNEAS --}}
            <div id="list-lineas" class="tab-body right-tab">
                <table class="t-audit">
                    <thead><tr><th>ID</th><th>Línea</th><th>Pertenece a Tipo</th><th style="text-align:right">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($lineas_list as $l)
                        <tr>
                            <td class="id-col">#{{ $l->id }}</td>
                            <td style="font-weight:600">{{ $l->nombre }}</td>
                            <td><span class="tag">{{ $l->tipo->nombre ?? 'Huérfano' }}</span></td>
                            <td style="text-align:right">
                                <button class="act-btn edit" onclick='editParam("linea", @json($l))'><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $l->id, 'tipo' => 'linea']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="act-btn del"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 4. TABLA TIPOS --}}
            <div id="list-tipos" class="tab-body right-tab">
                <table class="t-audit">
                    <thead><tr><th>ID</th><th>Tipo Contable</th><th>Uso</th><th style="text-align:right">Acciones</th></tr></thead>
                    <tbody>
                        @foreach($tipos_list as $t)
                        <tr>
                            <td class="id-col">#{{ $t->id }}</td>
                            <td style="font-weight:600">{{ $t->nombre }}</td>
                            <td>{{ $t->subgrupos_count }} items</td>
                            <td style="text-align:right">
                                <button class="act-btn edit" onclick='editParam("tipo", @json($t))'><i class="bi bi-pencil-square"></i></button>
                                <form action="{{ route('inventario.clasificacion.parametro.destroy', ['id' => $t->id, 'tipo' => 'tipo']) }}" method="POST" style="display:inline" onsubmit="return confirm('¿Borrar?');">@csrf @method('DELETE')<button class="act-btn del"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- LÓGICA JAVASCRIPT --}}
    <script>
        // --- 1. GESTIÓN DE PESTAÑAS ---
        function switchTab(side, targetId) {
            // side: 'left' o 'right'
            const containerClass = side === 'left' ? '.left-tab' : '.right-tab';
            const btnGroup = side === 'left' ? '#btn-left-main, #btn-left-cats' : '.card-box:last-child .tab-link';
            
            // Ocultar contenidos
            document.querySelectorAll(containerClass).forEach(el => el.classList.remove('active'));
            document.getElementById(targetId).classList.add('active');

            // Actualizar botones (Visual)
            if(side === 'right') {
                document.querySelectorAll(btnGroup).forEach(b => b.classList.remove('active'));
                event.target.classList.add('active');
            } else {
                document.getElementById('btn-left-main').classList.remove('active');
                document.getElementById('btn-left-cats').classList.remove('active');
                // Activar el botón correspondiente al ID del tab
                if(targetId === 'form-main') document.getElementById('btn-left-main').classList.add('active');
                else document.getElementById('btn-left-cats').classList.add('active');
            }
        }

        // --- 2. EDITAR VINCULACIÓN (PRINCIPAL) ---
        function editMain(data) {
            switchTab('left', 'form-main'); // Forzar cambio de tab
            document.getElementById('title-main').innerText = "Editando Subgrupo #" + data.id;
            document.getElementById('btnMain').innerText = "Actualizar Vinculación";
            document.getElementById('btnMain').style.background = "#4f46e5";
            document.getElementById('cancelMain').style.display = "block";
            
            // Llenar datos
            document.getElementById('inpMainName').value = data.nombre;
            document.getElementById('selMainTipo').value = data.id_InvTipos;
            document.getElementById('selMainLinea').value = data.id_InvLineas;
            document.getElementById('selMainGrupo').value = data.id_InvGrupos;

            // Cambiar ruta
            const form = document.getElementById('formMain');
            form.action = "{{ url('inventario/clasificacion') }}/" + data.id;
            document.getElementById('methodMain').value = "PUT";
        }

        function resetMain() {
            document.getElementById('title-main').innerText = "Nueva Vinculación";
            document.getElementById('btnMain').innerText = "Guardar Vinculación";
            document.getElementById('btnMain').style.background = "#0f172a";
            document.getElementById('cancelMain').style.display = "none";
            
            document.getElementById('formMain').reset();
            document.getElementById('formMain').action = "{{ route('inventario.clasificacion.store') }}";
            document.getElementById('methodMain').value = "POST";
        }

        // --- 3. EDITAR CATÁLOGOS BASE (TIPO, LÍNEA, GRUPO) ---
        function editParam(type, data) {
            switchTab('left', 'form-cats'); // Ir al tab de catálogos
            resetCats(); // Limpiar estados previos

            // Estilos visuales de "Editando"
            const box = document.getElementById('box-' + type);
            box.classList.add('editing');
            box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.getElementById('lbl-' + type).innerText = "EDITANDO #" + data.id;

            // Configurar Formulario
            const form = document.getElementById('form' + capitalize(type));
            // OJO: La URL debe coincidir con la ruta PUT definida en web.php
            form.action = "{{ url('inventario/clasificacion/update-') }}" + type + "/" + data.id;
            document.getElementById('method' + capitalize(type)).value = "PUT";

            // Llenar campos
            document.getElementById('inp' + capitalize(type) + 'Name').value = data.nombre;
            
            // Llenar selects padre si aplica
            if(type === 'linea') document.getElementById('inpLineaParent').value = data.id_InvTipos;
            if(type === 'grupo') document.getElementById('inpGrupoParent').value = data.id_InvLineas;
        }

        function resetCats() {
            ['tipo', 'linea', 'grupo'].forEach(t => {
                // Quitar estilos
                document.getElementById('box-' + t).classList.remove('editing');
                document.getElementById('lbl-' + t).innerText = "NUEVO";
                
                // Resetear form y ruta
                const form = document.getElementById('form' + capitalize(t));
                form.reset();
                form.action = "{{ url('inventario/clasificacion/store-') }}" + t;
                document.getElementById('method' + capitalize(t)).value = "POST";
            });
        }

        function capitalize(str) { return str.charAt(0).toUpperCase() + str.slice(1); }
    </script>
</x-base-layout>