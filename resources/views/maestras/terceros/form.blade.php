@php
    $tercero = $tercero ?? (object) [];
    $fieldIcons = [
        'cod_ter' => 'hash',
        'tip_prv' => 'award',
        'nom_ter' => 'user',
        'estado' => 'activity',
        'apl1' => 'user-check',
        'apl2' => 'user-check',
        'nom1' => 'user',
        'nom2' => 'user',
        'apell1' => 'user',
        'apell2' => 'user',
        'sexo' => 'users',
        'fec_nac' => 'calendar',
        'est_civil' => 'heart',
        'tdoc' => 'file-text',
        'dv' => 'hash',
        'digito_v' => 'hash',
        'razon_soc' => 'briefcase',
        'nom_conyug' => 'user-plus',
        'id_conyuge' => 'hash',
        'parentezco' => 'link',
        'mail_conyu' => 'mail',
        'num_hijos' => 'users',
        'fec_falle' => 'activity',
        'contacto' => 'phone',
        'cont_tel' => 'phone',
        'cargo' => 'briefcase',
        'dir' => 'map-pin',
        'ciu_comer' => 'map',
        'ciudad' => 'map',
        'dpto' => 'map',
        'pais' => 'globe',
        'tel' => 'phone',
        'cel' => 'smartphone',
        'email' => 'mail',
        'observ' => 'message-square',
        'fecha_lice' => 'calendar',
        'fecha_ipuc' => 'calendar',
        'congrega' => 'home',
        'cupo_cred' => 'credit-card',
        'clasific' => 'tag',
        'aut_ret' => 'file-minus',
    ];

    $ignore = ['id'];
    $groups = [
        'Identificación' => ['cod_ter', 'nom_ter', 'tip_prv'],
        'Información Personal' => [
            'estado',
            'apl1',
            'apl2',
            'nom1',
            'nom2',
            'sexo',
            'fec_nac',
            'est_civil',
            'tipo_ter',
            'tip_pers',
            'tdoc',
            'dv',
            'digito_v',
            'razon_soc',
            'raz',
            'nom_conyug',
            'id_conyuge',
            'parentezco',
            'mail_conyu',
            'num_hijos',
            'fec_falle',
            'contacto',
            'cont_tel',
            'cargo',
        ],
        'Ubicación' => [
            'dir',
            'dir1',
            'dir2',
            'dir_comer',
            'ciu_comer',
            'ciudad',
            'dpto',
            'mun',
            'pais',
            'cod_postal',
            'cod_pais',
            'cod_depa',
            'barrio',
            'lugar_naci',
            'lugar_expcc',
        ],
        'Contacto' => ['tel', 'tel1', 'tel2', 'cel', 'fax1', 'email'],
        'Iglesia' => [
            'fec_minis',
            'cod_dist',
            'fecha_lice',
            'fecha_ipuc',
            'fecha_aded',
            'fec_aport',
            'fec_cump',
            'fec_expcc',
            'congrega',
            'respon',
            'regimen',
            'cod_lice',
            'cod_clase',
        ],
        'Financiera' => [
            'cupo_cred',
            'ind_cred',
            'ind_rete',
            'ind_requ',
            'ind_items',
            'bloqueo',
            'bloq_aut',
            'bloq_tmk',
            'bloq_ate',
            'cta',
            'cta_ban',
            'cta_icap',
            'cta_icac',
            'cod_ban',
            'por_cred',
            'pla_com',
            'por_com',
            'por_comi',
            'por_des',
            'cupo_cxc',
            'i_cupocc',
            'i_cupocp',
            'cupo_cxp',
            'int_mora',
            'dia_plaz',
            'dia_com',
            'dia_adp',
            'prec_rem',
            'lista_prec',
            'icrecon',
        ],
        'Comercial' => [
            'clasific',
            'cod_can',
            'cod_ven',
            'cod_ven1',
            'cod_ven2',
            'cod_ven3',
            'cod_ven4',
            'cod_zona',
            'cod_activ',
            'cod_act',
            'cod_cla',
            'tip_cli',
            'clas_cli',
            'esp_gab',
            'conta',
            'uni_fra',
            'dto_det',
            'ind_mayor',
            'ind_iva',
            'ind_ret',
            'ind_doc',
            'ind_tmk',
            'ind_cree',
        ],
        'Tributaria' => [
            'cod_respfiscal',
            'cod_tributo',
            'codimpuesto',
            'codpostal',
            'Cod_acteco',
            'cod_est',
            'inf_ter',
        ],
        'Otros' => [
            'observ',
            'matricula',
            'exten',
            'dp1',
            'dp2',
            'dp3',
            'pc1',
            'pc2',
            'pc3',
            'r_semana',
            'pago',
            'pago1',
            'suc_cli',
            'cod_suc',
        ],
    ];
@endphp

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ $buttonText }} Tercero</h5>
    </div>
    <form method="POST" action="{{ $action }}" class="card-body" id="form{{ $buttonText }}Ter" novalidate>
        @csrf
        @if ($method ?? false)
            @method($method)
        @endif

        <div class="accordion proposal-faq-accordion" id="accordionTercero">
            @foreach ($groups as $section => $fields)
                <x-maestras.terceros.accordion-item :id="Str::slug($section)" :title="$section" :open="$loop->first">
                    <div class="row g-3">
                        @foreach ($fields as $field)
                            @if (!in_array($field, $ignore))
                                <div class="col-md-4">
                                    @php
                                        $value = old($field, $tercero->$field ?? '');
                                        $icon = $fieldIcons[$field] ?? 'square';
                                    @endphp
                                    <label class="form-label fw-semibold text-capitalize">
                                        <i
                                            class="feather-{{ $icon }} me-1 text-primary"></i>{{ str_replace('_', ' ', $field) }}
                                    </label>
                                    @if ($field === 'cod_ter')                                        
                                            <input type="text" class="form-control bg-light-subtle"
                                                value="{{ $tercero->cod_ter }}" disabled>                                        
                                    @elseif($field === 'tip_prv')
                                        <select name="tip_prv"
                                            class="form-select @error('tip_prv') is-invalid @enderror">
                                            @foreach ($tipos as $tipo)
                                                <option value="{{ $tipo->id }}" @selected($value == $tipo->id)>
                                                    {{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('tip_prv')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif($field === 'cod_dist')
                                        <select name="{{$field}}"class="form-select @error('cod_dist') is-invalid @enderror">
                                            @foreach ($distritos as $distrito)
                                                <option value="{{ $distrito->COD_DIST }}" @selected($value == $distrito->COD_DIST)>
                                                    {{ $distrito->NOM_DIST }}</option>
                                            @endforeach
                                        </select>
                                        @error('cod_dist')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror                                        
                                    @elseif($field === 'sexo')
                                        <select name="sexo" class="form-select @error('sexo') is-invalid @enderror">
                                            <option value="">Seleccione...</option>
                                            <option value="V" @selected($value == 'V')>Masculino</option>
                                            <option value="H" @selected($value == 'H')>Femenino</option>
                                        </select>
                                    @elseif(str_contains($field, 'email'))
                                        <input type="email" name="{{ $field }}"
                                            class="form-control @error($field) is-invalid @enderror"
                                            placeholder="correo@ejemplo.com" value="{{ $value }}">
                                        @error($field)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif(str_contains($field, 'tel') || str_contains($field, 'cel') || str_contains($field, 'fax'))
                                        <input type="tel" name="{{ $field }}"
                                            class="form-control @error($field) is-invalid @enderror"
                                            placeholder="Ingrese número" value="{{ $value }}">
                                        @error($field)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @elseif(in_array($field, ['observ', 'razon_soc', 'nom_conyug']))
                                        <textarea name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" rows="2"
                                            placeholder="Ingrese {{ str_replace('_', ' ', $field) }}">{{ $value }}</textarea>
                                        @error($field)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <input type="text" name="{{ $field }}"
                                            class="form-control @error($field) is-invalid @enderror"
                                            placeholder="Ingrese {{ str_replace('_', ' ', $field) }}"
                                            value="{{ $value }}">
                                        @error($field)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </x-maestras.terceros.accordion-item>
            @endforeach
        </div>

        <div class="d-flex flex-row-reverse gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="feather-save me-2"></i> {{ $buttonText }}
            </button>
            <a href="{{ route('maestras.terceros.index') }}" class="btn btn-light">Cancelar</a>
        </div>
    </form>
</div>

<script>
    $('#form{{ $buttonText }}Ter').submit(function(event) {
        var form = this;
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            event.preventDefault();
            event.stopPropagation();
        }
    });
</script>
