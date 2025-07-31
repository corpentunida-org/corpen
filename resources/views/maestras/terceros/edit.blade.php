{{-- resources/views/maestras/terceros/edit.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Editar Tercero')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-vcard"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{ $tercero->nom_ter }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Cédula: {{ $tercero->cod_ter }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información General
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">

                    
                    <form method="POST" action="{{ route('maestras.terceros.update', $tercero->cod_ter) }}">

                        @csrf
                        @method('PUT')

                        <div class="accordion" id="accordionTercero">
                            @php
                                $fields = $tercero->toArray();
                                $ignore = ['id', 'cod_ter'];
                                $groups = [
                                    'Información Personal' => ['nom_ter','estado','apl1','apl2','nom1','nom2','apell1','apell2','sexo','fec_nac','est_civil','tipo_ter','tip_pers','tdoc','dv','digito_v','razon_soc','raz','nom_conyug','id_conyuge','parentezco','mail_conyu','num_hijos','fec_falle','contacto','cont_tel','cargo'],
                                    'Ubicación' => ['dir','dir1','dir2','dir_comer','ciu_comer','ciudad','dpto','mun','pais','cod_postal','cod_pais','cod_depa','barrio','lugar_naci','lugar_expcc'],
                                    'Contacto' => ['tel','tel1','tel2','cel','fax1','email','email_fe','email_fac'],
                                    'Labor e Iglesia' => ['fecha_lice','fecha_ipuc','fecha_aded','fec_aport','fec_cump','fec_expcc','congrega','respon','regimen','cod_lice','cod_clase'],
                                    'Financiera' => ['cupo_cred','ind_cred','ind_rete','ind_requ','ind_items','bloqueo','bloq_aut','bloq_tmk','bloq_ate','cta','cta_ban','cta_icap','cta_icac','cod_ban','por_cred','pla_com','por_com','por_comi','por_des','cupo_cxc','i_cupocc','i_cupocp','cupo_cxp','int_mora','dia_plaz','dia_com','dia_adp','prec_rem','lista_prec','icrecon'],
                                    'Comercial' => ['clasific','cod_can','cod_ven','cod_ven1','cod_ven2','cod_ven3','cod_ven4','cod_zona','cod_activ','cod_act','cod_cla','tip_cli','clas_cli','esp_gab','conta','uni_fra','dto_det','ind_mayor','ind_iva','ind_ret','ind_doc','indpcom','ind_tmk','ind_cree'],
                                    'Tributaria' => ['aut_ret','ret_iva','ret_ica','ret_prv','cod_respfiscal','cod_tributo','codimpuesto','codpostal','Cod_acteco','fec_minis','cod_dist','cod_est','inf_ter'],
                                    'Otros' => ['observ','fecha_lice','matricula','exten','dp1','dp2','dp3','pc1','pc2','pc3','r_semana','pago','pago1','suc_cli','cod_suc','suc_cli','fecha_aded','fec_dat']
                                ];
                            @endphp

                            @foreach($groups as $section => $names)
                                <x-maestras.terceros.accordion-item :id="Str::slug($section)" :title="$section" :open="$loop->first">
                                    <div class="row">
                                        @foreach($names as $field)
                                            @if(!in_array($field, $ignore))
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label text-capitalize">{{ str_replace('_', ' ', $field) }}</label>
                                                    <input type="text" class="form-control" name="{{ $field }}" value="{{ old($field, $tercero->$field) }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </x-maestras.terceros.accordion-item>
                            @endforeach
                        </div>

                        <div class="d-flex flex-row-reverse gap-2 mt-4">

                            <button class="btn btn-warning" type="submit">
                                <i class="feather-save me-2"></i> Actualizar Tercero
                            </button>

                            <a href="{{ route('maestras.terceros.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>

                    <script>
                        (function () {
                            'use strict';
                            const form = document.getElementById('formUpdateTercero');
                            form.addEventListener('submit', function (event) {
                                if (!form.checkValidity()) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            }, false);
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
