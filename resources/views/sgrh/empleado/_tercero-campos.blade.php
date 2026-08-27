{{--
    Partial compartido entre tercero-edit.blade.php y tercero-show.blade.php.
    Espera: $tercero, $tipos, $tiposDocumento, $estadosCiviles, $fieldIcons, $fieldLabels,
    $groups, $uppercaseFields, $tdocPredeterminado, $tdocCodigosInvalidosConocidos, $editable
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

                        @if ($field === 'tip_prv')
                            <select name="tip_prv" class="form-select @error('tip_prv') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="">Sin clasificar</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" @selected($value == $tipo->id)>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        @elseif ($field === 'sexo')
                            <select name="sexo" class="form-select @error('sexo') is-invalid @enderror" {{ $disabledAttr }}>
                                <option value="">Seleccione...</option>
                                <option value="V" @selected($value == 'V')>Masculino</option>
                                <option value="H" @selected($value == 'H')>Femenino</option>
                            </select>
                        @elseif ($field === 'tdoc')
                            <select name="tdoc" class="form-select @error('tdoc') is-invalid @enderror" {{ $disabledAttr }}>
                                @foreach ($tiposDocumento as $codigo => $etiqueta)
                                    <option value="{{ $codigo }}"
                                        @selected((string) $value === $codigo || ((string) $value === '' && $codigo === $tdocPredeterminado))>
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
                                    <option value="{{ $codigo }}" @selected((string) $value === $codigo)>{{ $etiqueta }}</option>
                                @endforeach
                                @if ((string) $value !== '' && !$estadosCiviles->has((string) $value))
                                    <option value="{{ $value }}" selected>{{ $value }} (valor actual sin mapear)</option>
                                @endif
                            </select>
                        @elseif (in_array($field, ['fec_nac', 'fec_falle']))
                            <input type="date" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" {{ $disabledAttr }}
                                   value="{{ $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '' }}">
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
