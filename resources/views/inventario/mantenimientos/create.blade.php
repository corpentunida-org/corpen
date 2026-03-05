<x-base-layout>
    <style>
        .maint-wrapper { max-width: 800px; margin: 40px auto; font-family: 'Inter', system-ui, sans-serif; color: #0f172a; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); padding: 40px; border: 1px solid #f1f5f9; }
        
        .head-title { text-align: center; margin-bottom: 30px; }
        .head-title h2 { font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
        .head-title p { color: #64748b; font-size: 0.95rem; margin-top: 5px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } }

        .field { margin-bottom: 20px; position: relative; }
        .label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 8px; color: #334155; }
        
        .input { width: 100%; padding: 12px 16px; border-radius: 10px; border: 1px solid #cbd5e1; font-size: 0.95rem; background: #f8fafc; transition: all 0.2s; box-sizing: border-box; }
        .input:focus { outline: none; border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        
        .cost-input { position: relative; }
        .currency { position: absolute; left: 16px; top: 13px; font-weight: bold; color: #64748b; }
        .cost-field { padding-left: 35px; font-family: 'Fira Code', monospace; font-weight: 600; color: #0f172a; }

        /* --- ESTILOS DEL BUSCADOR AUTOCOMPLETE --- */
        .autocomplete-list { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; margin-top: 4px; max-height: 200px; overflow-y: auto; z-index: 50; display: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .autocomplete-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #334155; }
        .autocomplete-item:last-child { border-bottom: none; }
        .autocomplete-item:hover { background: #e0e7ff; color: #4338ca; font-weight: 500; }
        .clear-btn { position: absolute; right: 12px; top: 38px; cursor: pointer; color: #94a3b8; display: none; }
        .clear-btn:hover { color: #ef4444; }

        /* --- ESTILOS DE ARCHIVO --- */
        input[type="file"] { display: none; }
        .custom-file-upload { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; cursor: pointer; transition: all 0.2s; text-align: center; }
        .custom-file-upload:hover { background: #f1f5f9; border-color: #94a3b8; }
        .custom-file-upload.has-file { border-style: solid; border-color: #10b981; background: #ecfdf5; }
        .upload-icon { margin-bottom: 10px; color: #64748b; }
        .upload-text { font-size: 0.9rem; font-weight: 600; color: #334155; }
        .upload-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 5px; }

        .btn-submit { width: 100%; background: #0f172a; color: #fff; padding: 16px; border-radius: 12px; font-weight: 700; font-size: 1rem; border: none; cursor: pointer; margin-top: 10px; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2); }
        .btn-submit:hover { background: #334155; transform: translateY(-2px); box-shadow: 0 6px 10px -1px rgba(15, 23, 42, 0.3); }

        .error-text { color: #ef4444; font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500; }
        .alert { padding: 16px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; font-size: 0.9rem; border: 1px solid transparent; }
        .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    </style>

    <div class="maint-wrapper">
        @if(session('error'))
            <div class="alert alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="head-title">
                <h2>Registrar Mantenimiento</h2>
                <p>Ingrese los detalles del servicio técnico y asigne a los responsables.</p>
            </div>

            <form action="{{ route('inventario.mantenimientos.store') }}" method="POST" enctype="multipart/form-data" id="mantenimientoForm">
                @csrf
                
                <div class="field">
                    <label class="label">Activo Afectado</label>
                    <input type="text" id="search_activos" class="input" placeholder="Escribe la placa o nombre del activo..." autocomplete="off">
                    <span class="clear-btn" id="clear_activos">✖</span>
                    <input type="hidden" name="id_InvActivos" id="id_InvActivos" value="{{ old('id_InvActivos') }}">
                    <div id="list_activos" class="autocomplete-list"></div>
                    @error('id_InvActivos') <span class="error-text">Debe seleccionar un activo válido de la lista.</span> @enderror
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label class="label">Técnico / Persona que Registra</label>
                        <input type="text" id="search_tecnico" class="input" placeholder="Buscar técnico..." autocomplete="off">
                        <span class="clear-btn" id="clear_tecnico">✖</span>
                        <input type="hidden" name="id_usersRegistro" id="id_usersRegistro" value="{{ old('id_usersRegistro', auth()->id()) }}">
                        <div id="list_tecnico" class="autocomplete-list"></div>
                        @error('id_usersRegistro') <span class="error-text">Seleccione un técnico válido.</span> @enderror
                    </div>

                    <div class="field">
                        <label class="label">Asignado a (Responsable Temporal)</label>
                        <input type="text" id="search_asignado" class="input" placeholder="Buscar responsable..." autocomplete="off">
                        <span class="clear-btn" id="clear_asignado">✖</span>
                        <input type="hidden" name="id_usersAsignado" id="id_usersAsignado" value="{{ old('id_usersAsignado', auth()->id()) }}">
                        <div id="list_asignado" class="autocomplete-list"></div>
                        @error('id_usersAsignado') <span class="error-text">Seleccione un responsable válido.</span> @enderror
                    </div>
                </div>

                <div class="field">
                    <label class="label">Costo del Servicio</label>
                    <div class="cost-input">
                        <span class="currency">$</span>
                        <input type="text" id="costo_visual" class="input cost-field" placeholder="0">
                        
                        <input type="hidden" name="costo_mantenimiento" id="costo_real" value="{{ old('costo_mantenimiento') }}">
                    </div>
                    @error('costo_mantenimiento') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label class="label">Detalle Técnico / Reparación</label>
                    <textarea name="detalle" class="input" rows="4" placeholder="Describa el fallo, diagnóstico y la solución aplicada...">{{ old('detalle') }}</textarea>
                    @error('detalle') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <!-- <div class="field">
                    <label class="label">Factura / Soporte (Opcional)</label>
                    <label for="acta_archivo" class="custom-file-upload" id="upload-box">
                        <div class="upload-icon" id="upload-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        </div>
                        <span class="upload-text" id="upload-text">Haz clic para subir un archivo</span>
                        <span class="upload-hint">Soporta PDF, JPG o PNG (Max 5MB)</span>
                    </label>
                    <input type="file" name="acta_archivo" id="acta_archivo" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this)">
                    @error('acta_archivo') <span class="error-text">{{ $message }}</span> @enderror
                </div> -->

                <button type="submit" class="btn-submit">Guardar Registro</button>
            </form>
        </div>
    </div>

    @php
        $activosJson = $activos->map(fn($a) => ['id' => $a->id, 'text' => $a->codigo_activo . ' - ' . $a->nombre])->values()->toJson();
        $usuariosJson = $usuarios->map(fn($u) => ['id' => $u->id, 'text' => $u->name])->values()->toJson();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. LÓGICA DEL COSTO CON SEPARADOR DE MILES ---
            const costoVisual = document.getElementById('costo_visual');
            const costoReal = document.getElementById('costo_real');

            // Formateador de moneda estilo Colombia (ej. 1.500.000)
            const formateadorMoneda = new Intl.NumberFormat('es-CO');

            // Si hay un valor previo (por error de validación en Laravel) lo formateamos al cargar
            if (costoReal.value) {
                costoVisual.value = formateadorMoneda.format(costoReal.value);
            }

            costoVisual.addEventListener('input', function(e) {
                // Eliminar cualquier cosa que NO sea número (letras, puntos, comas)
                let numeroLimpio = this.value.replace(/\D/g, '');
                
                if (numeroLimpio === '') {
                    costoReal.value = '';
                    this.value = '';
                    return;
                }

                // Guardamos el número real limpio en el input oculto para Laravel
                costoReal.value = numeroLimpio;

                // Mostramos el número formateado con puntos al usuario
                this.value = formateadorMoneda.format(numeroLimpio);
            });


            // --- 2. LÓGICA DE LOS BUSCADORES (AUTOCOMPLETE) ---
            const dataActivos = {!! $activosJson !!};
            const dataUsuarios = {!! $usuariosJson !!};

            function setupAutocomplete(inputId, hiddenId, listId, clearId, dataArray) {
                const input = document.getElementById(inputId);
                const hidden = document.getElementById(hiddenId);
                const list = document.getElementById(listId);
                const clearBtn = document.getElementById(clearId);

                if (hidden.value) {
                    const match = dataArray.find(i => i.id == hidden.value);
                    if (match) { 
                        input.value = match.text; 
                        clearBtn.style.display = 'block';
                    }
                }

                input.addEventListener('input', function() {
                    const val = this.value.toLowerCase().trim();
                    list.innerHTML = '';
                    hidden.value = ''; 
                    
                    if (val.length === 0) {
                        list.style.display = 'none';
                        clearBtn.style.display = 'none';
                        return;
                    }

                    clearBtn.style.display = 'block';

                    const matches = dataArray.filter(item => item.text.toLowerCase().includes(val)).slice(0, 15);
                    
                    if (matches.length > 0) {
                        matches.forEach(match => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = match.text;
                            
                            div.addEventListener('click', function() {
                                input.value = match.text;
                                hidden.value = match.id;
                                list.style.display = 'none';
                            });
                            list.appendChild(div);
                        });
                        list.style.display = 'block';
                    } else {
                        list.innerHTML = '<div class="autocomplete-item" style="color: #ef4444; pointer-events: none;">No se encontraron resultados...</div>';
                        list.style.display = 'block';
                    }
                });

                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    hidden.value = '';
                    list.style.display = 'none';
                    this.style.display = 'none';
                    input.focus();
                });

                document.addEventListener('click', function(e) {
                    if (e.target !== input) {
                        list.style.display = 'none';
                    }
                });
            }

            setupAutocomplete('search_activos', 'id_InvActivos', 'list_activos', 'clear_activos', dataActivos);
            setupAutocomplete('search_tecnico', 'id_usersRegistro', 'list_tecnico', 'clear_tecnico', dataUsuarios);
            setupAutocomplete('search_asignado', 'id_usersAsignado', 'list_asignado', 'clear_asignado', dataUsuarios);
            
            document.getElementById('mantenimientoForm').addEventListener('submit', function(e) {
                if(!document.getElementById('id_InvActivos').value) {
                    e.preventDefault();
                    alert('Por favor, busca y selecciona un Activo de la lista desplegable.');
                    document.getElementById('search_activos').focus();
                }
            });
        });

        // --- 3. SCRIPT DE ARCHIVO (UX) ---
        function updateFileName(input) {
            const box = document.getElementById('upload-box');
            const text = document.getElementById('upload-text');
            const icon = document.getElementById('upload-icon');

            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                text.innerText = fileName;
                box.classList.add('has-file');
                icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
            } else {
                text.innerText = 'Haz clic para subir un archivo';
                box.classList.remove('has-file');
                icon.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>';
            }
        }
    </script>
</x-base-layout>