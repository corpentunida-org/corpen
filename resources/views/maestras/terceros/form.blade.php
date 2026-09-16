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
        'parentesco' => 'link',
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

    // Antes se mostraba el nombre crudo de la columna (str_replace('_',' ',$field)) como
    // etiqueta — así fue como "apl1"/"nom1" (apellido/nombre) se terminaron confundiendo al
    // diligenciar el formulario a mano: nada en pantalla decía cuál era apellido y cuál nombre.
    $fieldLabels = [
        // Identificación
        'cod_ter' => 'Cédula / NIT',
        'nom_ter' => 'Nombre / Razón Social',
        'tip_prv' => 'Tipo de Tercero',
        'estado' => 'Estado',
        'tipo_ter' => 'Tipo de Tercero (Catálogo)',
        'tip_pers' => 'Tipo de Persona',
        'tip_cli' => 'Tipo de Cliente',
        'tdoc' => 'Tipo de Documento',
        'dv' => 'Dígito de Verificación',
        'digito_v' => 'Dígito de Verificación (alterno)',
        'id_ter' => 'ID Externo',

        // Información personal
        'apl1' => 'Primer Apellido',
        'apl2' => 'Segundo Apellido',
        'nom1' => 'Primer Nombre',
        'nom2' => 'Segundo Nombre',
        'sexo' => 'Sexo',
        'fec_nac' => 'Fecha de Nacimiento',
        'est_civil' => 'Estado Civil',
        'razon_soc' => 'Razón Social',
        'raz' => 'Razón Social (código)',
        'repres' => 'Representante Legal',
        'cargo' => 'Cargo',

        // Cónyuge / familia
        'nom_conyug' => 'Nombre del Cónyuge',
        'id_conyuge' => 'Cédula del Cónyuge',
        'parentesco' => 'Parentesco',
        'mail_conyu' => 'Correo del Cónyuge',
        'num_hijos' => 'Número de Hijos',
        'fec_falle' => 'Fecha de Fallecimiento',
        'contacto' => 'Persona de Contacto',
        'cont_cxc' => 'Contacto Cuentas por Cobrar',
        'cont_tel' => 'Teléfono de Contacto',

        // Ubicación
        'dir' => 'Dirección',
        'dir1' => 'Dirección 1',
        'dir2' => 'Dirección 2',
        'dir_comer' => 'Dirección Comercial',
        'ciu_comer' => 'Ciudad Comercial',
        'ciudad' => 'Ciudad',
        'cod_ciu' => 'Código Ciudad',
        'dpto' => 'Departamento',
        'depa' => 'Departamento (texto)',
        'mun' => 'Municipio',
        'pais' => 'País',
        'cod_pais' => 'Código País',
        'cod_depa' => 'Código Departamento',
        'cod_postal' => 'Código Postal',
        'codpostal' => 'Código Postal (2)',
        'barrio' => 'Barrio',
        'lugar_naci' => 'Lugar de Nacimiento',
        'lugar_expcc' => 'Lugar de Expedición de Cédula',
        'exten' => 'Extensión',

        // Contacto
        'tel' => 'Teléfono',
        'tel1' => 'Teléfono 1',
        'tel2' => 'Teléfono 2',
        'cel' => 'Celular',
        'fax1' => 'Fax',
        'email' => 'Correo Electrónico',
        'email_fac' => 'Correo de Facturación',
        'email_fact' => 'Correo de Facturación (2)',
        'email_fe' => 'Correo Factura Electrónica',

        // Iglesia
        'fec_minis' => 'Fecha Inicio Ministerio',
        'cod_dist' => 'Distrito',
        'fecha_lice' => 'Fecha de Licencia',
        'fecha_ipuc' => 'Fecha de Ingreso IPUC',
        'fecha_aded' => 'Fecha de Actualización de Datos',
        'fec_aport' => 'Fecha de Aporte',
        'fec_cump' => 'Fecha de Cumpleaños',
        'fec_expcc' => 'Fecha de Expedición de Cédula',
        'congrega' => 'Congregación',
        'respon' => 'Responsable',
        'regimen' => 'Régimen Tributario',
        'cod_lice' => 'Código de Licencia',
        'cod_clase' => 'Código de Clase (Pastor)',
        'cod_est' => 'Código de Estado',

        // Financiera
        'cupo_cred' => 'Cupo de Crédito',
        'ind_cred' => 'Indicador de Crédito',
        'ind_rete' => 'Indicador de Retención',
        'ind_ret' => 'Indicador de Retención (2)',
        'ind_requ' => 'Indicador de Requerimiento',
        'ind_items' => 'Indicador de Ítems',
        'bloqueo' => 'Bloqueo',
        'bloq_aut' => 'Bloqueo Automático',
        'bloq_tmk' => 'Bloqueo Telemercadeo',
        'bloq_ate' => 'Bloqueo Atención',
        'exo_bloq' => 'Exonerado de Bloqueo',
        'cta' => 'Cuenta',
        'cta_ban' => 'Cuenta Bancaria',
        'cta_icap' => 'Cuenta ICA (P)',
        'cta_icac' => 'Cuenta ICA (C)',
        'cod_ban' => 'Código Banco',
        'por_cred' => 'Porcentaje de Crédito',
        'pla_com' => 'Plazo Comercial',
        'por_com' => 'Porcentaje Comisión',
        'por_comi' => 'Porcentaje Comisión (2)',
        'por_des' => 'Porcentaje Descuento',
        'cupo_cxc' => 'Cupo Cuentas por Cobrar',
        'i_cupocc' => 'Cupo CxC (interno)',
        'i_cupocp' => 'Cupo CxP (interno)',
        'cupo_cxp' => 'Cupo Cuentas por Pagar',
        'int_mora' => 'Interés de Mora',
        'dia_plaz' => 'Días de Plazo',
        'dia_com' => 'Días de Comisión',
        'dia_adp' => 'Días de Anticipo',
        'prec_rem' => 'Precio Remisión',
        'lista_prec' => 'Lista de Precios',
        'icrecon' => 'ICR Económico',
        'ret_iva' => 'Retención IVA',
        'rtiva' => 'Responsable Retención IVA',
        'ret_ica' => 'Retención ICA',
        'rtica' => 'Responsable Retención ICA',
        'ret_prv' => 'Retención Proveedor',

        // Comercial
        'clasific' => 'Clasificación',
        'clas_cli' => 'Clasificación de Cliente',
        'cod_can' => 'Código de Canal',
        'cod_ven' => 'Código Vendedor',
        'cod_ven1' => 'Código Vendedor 1',
        'cod_ven2' => 'Código Vendedor 2',
        'cod_ven3' => 'Código Vendedor 3',
        'cod_ven4' => 'Código Vendedor 4',
        'cod_zona' => 'Código Zona',
        'cod_activ' => 'Código Actividad Económica',
        'cod_act' => 'Código Actividad',
        'cod_cla' => 'Código Clase',
        'esp_gab' => 'Especialidad Gabinete',
        'conta' => 'Contador',
        'uni_fra' => 'Unidad de Franquicia',
        'dto_det' => 'Descuento Detalle',
        'ind_mayor' => 'Indicador Mayorista',
        'ind_iva' => 'Indicador de IVA',
        'ind_doc' => 'Indicador Documento',
        'ind_tmk' => 'Indicador Telemercadeo',
        'ind_cree' => 'Indicador CREE',
        'ind_suc' => 'Indicador Sucursal',
        'suc_cli' => 'Sucursal Cliente',
        'cod_suc' => 'Código Sucursal',
        'cod_bod' => 'Código Bodega',
        'r_semana' => 'Recaudo Semanal',
        'pago' => 'Forma de Pago',
        'pago1' => 'Forma de Pago (2)',
        'indpcom' => 'Indicador Porcentaje Comisión',
        'pc1' => 'Porcentaje Comisión 1',
        'pc2' => 'Porcentaje Comisión 2',
        'pc3' => 'Porcentaje Comisión 3',
        'dp1' => 'Descuento Producto 1',
        'dp2' => 'Descuento Producto 2',
        'dp3' => 'Descuento Producto 3',

        // Tributaria
        'cod_respfiscal' => 'Responsabilidad Fiscal',
        'cod_tributo' => 'Código Tributo',
        'codimpuesto' => 'Código de Impuesto',
        'Cod_acteco' => 'Código Actividad Económica (CIIU)',
        'inf_ter' => 'Información Adicional',

        // Otros
        'observ' => 'Observaciones',
        'matricula' => 'Matrícula',
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
            'parentesco',
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
                                        $label = $fieldLabels[$field] ?? Str::title(str_replace('_', ' ', $field));
                                    @endphp
                                    <label class="form-label fw-semibold">
                                        <i
                                            class="feather-{{ $icon }} me-1 text-primary"></i>{{ $label }}
                                    </label>
                                    @if ($field === 'cod_ter')
                                        @if (isset($tercero->cod_ter) && $tercero->cod_ter)
                                            <input type="text" class="form-control bg-light-subtle"
                                                value="{{ $tercero->cod_ter }}" disabled>
                                        @else
                                            <input type="text" name="cod_ter"
                                                class="form-control @error('cod_ter') is-invalid @enderror"
                                                placeholder="Ingrese código" value="{{ $value }}">
                                            @error('cod_ter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
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
                                        @php
                                            $distritoActual = $distritos->firstWhere('COD_DIST', $value);
                                        @endphp
                                        <input type="text" class="form-control bg-light-subtle" disabled
                                            value="{{ $distritoActual->NOM_DIST ?? ($value ?: 'Sin distrito') }}">
                                        <small class="text-muted">Se toma automáticamente de la congregación — no se edita aquí.</small>
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
                                    @elseif($field === 'congrega')
                                        @php
                                            $congregacionActual = \App\Models\Maestras\MaeCongregacion::where('codigo', $value)->first();
                                        @endphp
                                        <input type="text" class="form-control bg-light-subtle" disabled
                                            value="{{ $congregacionActual->nombre ?? ($value ?: 'Sin congregación') }}">
                                        <small class="text-muted">Se toma automáticamente de la congregación — no se edita aquí.</small>
                                    @elseif(in_array($field, ['observ', 'razon_soc', 'nom_conyug']))
                                        <textarea name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" rows="2"
                                            placeholder="Ingrese {{ $label }}">{{ $value }}</textarea>
                                        @error($field)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <input type="text" name="{{ $field }}"
                                            class="form-control @error($field) is-invalid @enderror"
                                            placeholder="Ingrese {{ $label }}"
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
