{{--
    Partial compartido entre tercero-edit.blade.php y tercero-show.blade.php.
    Espera: $tercero, $tipos, $tiposDocumento, $estadosCiviles, $parentescos, $departamentos,
    $municipios, $paises, $fieldIcons, $fieldLabels, $groups, $uppercaseFields,
    $tdocPredeterminado, $tdocCodigosInvalidosConocidos, $editable
--}}
<div class="accordion" id="accordionTerceroSgrh">
    @foreach ($groups as $section => $fields)
        <x-maestras.terceros.accordion-item :id="'sgrh-' . \Illuminate\Support\Str::slug($section)"
            :title="$section" :open="$loop->first">
            <div class="row g-3">
                @if ($section === 'Identificación')
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-hash me-1 text-primary"></i> Cod Ter
                        </label>
                        <input type="text" class="form-control bg-light-subtle" value="{{ $tercero->cod_ter }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-user me-1 text-primary"></i> Nombre completo
                        </label>
                        <input type="text" class="form-control bg-light-subtle" value="{{ $tercero->nom_ter }}" disabled>
                        @if ($editable)
                            <div class="form-text">Se actualiza automáticamente al guardar los cambios en Información Personal.</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-award me-1 text-primary"></i> Tipo de tercero
                        </label>
                        <input type="text" class="form-control bg-light-subtle"
                               value="{{ $tipos->firstWhere('id', (int) $tercero->tip_prv)?->nombre ?? 'Sin clasificar' }}" disabled>
                        @if ($editable)
                            <div class="form-text">Se actualiza automáticamente desde "Tipo (código interno)" en Información Personal.</div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-refresh-cw me-1 text-primary"></i> Fecha de actualización
                        </label>
                        <input type="text" class="form-control bg-light-subtle"
                               style="{{ ($desactualizado ?? false) ? 'color: #e11d48; font-weight: bold;' : '' }}"
                               value="{{ $tercero->fec_act ? \Illuminate\Support\Carbon::parse($tercero->fec_act)->format('d/m/Y') : 'Nunca actualizado' }}" disabled>
                        @if ($desactualizado ?? false)
                            <div class="form-text" style="color: #e11d48;">
                                <i class="bi bi-exclamation-triangle"></i> Información de usuario requiere actualizar.
                            </div>
                        @endif
                    </div>
                @endif

                @foreach ($fields as $field)
                    @php
                        $value = old($field, $tercero->$field ?? '');
                        $icon = $fieldIcons[$field] ?? 'square';
                        $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                        $disabledAttr = $editable ? '' : 'disabled';
                    @endphp
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-{{ $icon }} me-1 text-primary"></i>{{ $label }}
                        </label>

                        @if ($field === 'tipo_ter')
                            <select name="tipo_ter" class="form-select @error('tipo_ter') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="">Sin clasificar</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" @selected((string) $value === (string) $tipo->id)>{{ $tipo->nombre }}</option>
                                @endforeach
                                @if ((string) $value !== '' && !$tipos->contains('id', (int) $value))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear, ej. "A" = Asociado)</option>
                                @endif
                            </select>
                            @if ($editable)
                                <div class="form-text">Al guardar, también actualiza "Tipo de tercero" en Identificación.</div>
                            @endif
                        @elseif ($field === 'tip_pers')
                            <select name="tip_pers" class="form-select @error('tip_pers') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                <option value="1" @selected((string) $value === '1')>Natural</option>
                                <option value="2" @selected((string) $value === '2')>Jurídica</option>
                                @if (!in_array((string) $value, ['', '1', '2'], true))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'parentesco')
                            <select name="parentesco" class="form-select @error('parentesco') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                @foreach ($parentescos as $codigo => $nombre)
                                    <option value="{{ $codigo }}" @selected((string) $value === (string) $codigo)>{{ $nombre }}</option>
                                @endforeach
                                @if ((string) $value !== '' && !$parentescos->has((string) $value))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'estado')
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                <option value="1" @selected((string) $value === '1')>Activo</option>
                                <option value="0" @selected((string) $value === '0')>Inactivo</option>
                                @if (!in_array((string) $value, ['', '1', '0'], true))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'sexo')
                            <select name="sexo" class="form-select @error('sexo') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="">Seleccione...</option>
                                <option value="V" @selected($value == 'V')>Masculino</option>
                                <option value="H" @selected($value == 'H')>Femenino</option>
                            </select>
                        @elseif ($field === 'tdoc')
                            <select name="tdoc" id="campo_tdoc" class="form-select @error('tdoc') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                @foreach ($tiposDocumento as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}"
                                        @selected((string) $value === (string) $codigo || ((string) $value === '' && (string) $codigo === $tdocPredeterminado))>
                                        {{ $codigo }} - {{ $etiqueta }}
                                    </option>
                                @endforeach
                                @if ((string) $value !== '' && !$tiposDocumento->has((string) $value))
                                    <option value="{{ $value }}" selected>
                                        {{ $value }} - {{ $tdocCodigosInvalidosConocidos[(string) $value] ?? '(valor actual sin mapear, verificar)' }}
                                    </option>
                                @endif
                            </select>
                            @if (array_key_exists((string) $value, $tdocCodigosInvalidosConocidos))
                                <div class="form-text text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Este valor no es un tipo de documento válido{{ $editable ? ' — corrígelo seleccionando el tipo real.' : '.' }}
                                </div>
                            @endif
                        @elseif ($field === 'est_civil')
                            <select name="est_civil" class="form-select @error('est_civil') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                @foreach ($estadosCiviles as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}" @selected((string) $value === (string) $codigo)>{{ $etiqueta }}</option>
                                @endforeach
                                @if ((string) $value !== '' && !$estadosCiviles->has((string) $value))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'dpto')
                            @php
                                // 'dpto' y 'cod_depa' duplican el mismo código DANE, llenados de
                                // forma inconsistente entre registros — si 'dpto' está vacío/'0',
                                // se usa 'cod_depa' para mostrar el valor real. Al guardar, ambas
                                // columnas quedan sincronizadas (ver updateTercero()).
                                $valorDpto = old('dpto');
                                if ($valorDpto === null) {
                                    $valorDpto = ($tercero->dpto && $tercero->dpto !== '0') ? $tercero->dpto : $tercero->cod_depa;
                                }
                            @endphp
                            <select name="dpto" id="campo_dpto" class="form-select @error('dpto') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $valorDpto === '')>Sin definir</option>
                                @foreach ($departamentos as $codigoDane => $nombre)
                                    <option value="{{ $codigoDane }}" @selected((string) $valorDpto === (string) $codigoDane)>{{ $nombre }}</option>
                                @endforeach
                                @if ((string) $valorDpto !== '' && !$departamentos->has((string) $valorDpto))
                                    <option value="{{ $valorDpto }}" selected>{{ $valorDpto }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'mun')
                            @php
                                $municipioActual = $municipios->firstWhere('id', (int) $value);
                            @endphp
                            <select name="mun" id="campo_mun" class="form-select @error('mun') is-invalid @enderror" {{ $disabledAttr }}
                                    data-valor-actual="{{ $value }}">
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                @if ($municipioActual)
                                    <option value="{{ $municipioActual->id }}" selected>{{ $municipioActual->nombre }}</option>
                                @elseif ((string) $value !== '')
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                            <div class="form-text">Elige primero el departamento para filtrar los municipios.</div>
                        @elseif ($field === 'pais')
                            @php
                                $paisActual = $paises->first(fn($p) => strcasecmp($p->nombre, (string) $value) === 0);
                            @endphp
                            <select name="pais" class="form-select @error('pais') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="" @selected((string) $value === '')>Sin definir</option>
                                @foreach ($paises as $pais)
                                    <option value="{{ $pais->nombre }}" @selected($paisActual && $paisActual->codigo_iso === $pais->codigo_iso)>{{ $pais->nombre }}</option>
                                @endforeach
                                @if ((string) $value !== '' && !$paisActual)
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif ($field === 'dv')
                            @php
                                $esNit = (string) old('tdoc', $tercero->tdoc) === '31';
                            @endphp
                            <input type="text" name="dv" id="campo_dv" class="form-control @error('dv') is-invalid @enderror"
                                   {{ $editable && $esNit ? '' : 'disabled' }} {{ $editable && $esNit ? 'required' : '' }}
                                   value="{{ $value }}">
                            <div class="form-text">Solo aplica (y es obligatorio) cuando el tipo de documento es NIT.</div>
                        @elseif (in_array($field, ['fec_nac', 'fec_falle', 'fec_expcc']))
                            @php
                                // '1899-12-30' y '1900-01-01' son fechas "centinela" heredadas de
                                // una importación vieja (típico de Excel al tratar una celda vacía
                                // como el día 0) — no son fechas reales. Se muestran vacías; si el
                                // usuario guarda sin tocarlas, quedan en NULL (limpieza natural,
                                // no se tocan las demás ~21.000 filas del resto del sistema).
                                $fechaFormateada = $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '';
                                $esFechaCentinela = in_array($fechaFormateada, ['1899-12-30', '1900-01-01'], true);
                                $fechaMostrar = $esFechaCentinela ? '' : $fechaFormateada;
                            @endphp
                            <input type="date" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   value="{{ $fechaMostrar }}">
                            @if ($esFechaCentinela)
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> Tenía una fecha de relleno heredada del sistema anterior; se muestra vacía.
                                </div>
                            @endif
                        @elseif (in_array($field, ['lugar_expcc', 'lugar_naci']))
                            <input type="text" name="{{ $field }}" id="campo_{{ $field }}" autocomplete="off"
                                   class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   placeholder="Escribe al menos 3 letras para buscar un municipio..." value="{{ $value }}">
                            <div id="sugerencias_{{ $field }}" class="list-group position-relative" style="z-index: 5;"></div>
                            @if ($editable)
                                <div class="form-text">Es texto libre — el municipio es solo una sugerencia, no obliga a elegir uno.</div>
                            @endif
                        @elseif (str_contains($field, 'email') || str_contains($field, 'mail'))
                            <input type="email" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   placeholder="correo@ejemplo.com" value="{{ $value }}">
                        @elseif (str_contains($field, 'tel') || str_contains($field, 'cel') || str_contains($field, 'fax'))
                            <input type="tel" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   placeholder="Ingrese número" value="{{ $value }}">
                        @elseif (in_array($field, ['contacto', 'nom_conyug']))
                            <textarea name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" rows="2" {{ $disabledAttr }}
                                      @if ($editable && in_array($field, $uppercaseFields)) style="text-transform: uppercase;" @endif>{{ $value }}</textarea>
                        @else
                            <input type="text" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   @if ($editable && in_array($field, $uppercaseFields)) style="text-transform: uppercase;" @endif
                                   value="{{ $value }}">
                            @if ($editable && in_array($field, $uppercaseFields))
                                <div class="form-text">Se guarda en mayúsculas.</div>
                            @endif
                        @endif

                        @error($field)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
        </x-maestras.terceros.accordion-item>
    @endforeach
</div>
