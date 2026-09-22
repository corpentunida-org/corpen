<x-base-layout>
    @section('titlepage', 'Administrador')
    <x-error />
    <x-success />
    <div class="col-xxl-12 col-xl-12">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#securityTab"
                            aria-selected="true">Crear Usuario</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="securityTab" role="tabpanel">
                    <div class="col-lg-12 p-4">
                        <form method="POST" action="{{ route('admin.users.store') }}" id="formAddUser" novalidate>
                            @csrf
                            @method('POST')
                            <div class="mb-4">
                                <label class="form-label">Cédula<span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nid') is-invalid @enderror" id="nidInput"
                                       name="nid" value="{{ old('nid') }}" inputmode="numeric" maxlength="20" required>
                                @error('nid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text fs-13" id="nidFeedback"></div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Nombre y Apellidos<span class="text-danger">*</span></label>
                                <input type="text" class="form-control uppercase-input @error('name') is-invalid @enderror"
                                       name="name" id="nameInput" value="{{ old('name') }}" readonly required
                                       placeholder="Se completa al escribir una cédula válida...">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text fs-13">Viene de Terceros — no se escribe a mano.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Correo corporativo<span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>


                            <div class="mb-4">
                                <label class="form-label">Contraseña<span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('pass') is-invalid @enderror" name="pass" required>
                                @error('pass') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Perfil<span class="text-danger">*</span></label>
                                <select class="form-control @error('rol') is-invalid @enderror" name="rol" required>
                                    <option value="" disabled selected>Seleccione un perfil...</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->id }}" @selected(old('rol') == $rol->id)>
                                            {{ strtoupper($rol->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text fs-13">Cada usuario tiene un solo perfil; define sus menús y permisos.</div>
                            </div>

                            <div class="d-flex flex-row-reverse gap-2 mt-2">
                                <button class="btn btn-success mt-4" data-bs-toggle="tooltip" title="Timesheets"
                                    type="submit">
                                    <i class="feather-plus me-2"></i>
                                    <span>Agregar Usuario</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            // Confirma en vivo si la cédula ya existe como tercero — antes de llegar al submit,
            // que exige lo mismo (exists:MaeTerceros) pero solo avisa tras enviar el formulario.
            let temporizadorNid;
            $('#nidInput').on('input', function () {
                clearTimeout(temporizadorNid);
                const cedula = $(this).val().trim();
                const feedback = $('#nidFeedback');
                $('#nameInput').val(''); // el nombre se vuelve a llenar solo cuando la cédula confirme
                if (cedula === '') { feedback.text(''); return; }
                feedback.removeClass('text-success text-danger').addClass('text-muted').text('Verificando...');
                temporizadorNid = setTimeout(function () {
                    $.getJSON('{{ route('admin.users.buscar-tercero') }}', { cedula: cedula })
                        .done(function (r) {
                            feedback.removeClass('text-muted text-success text-danger');
                            if (!r.encontrado) {
                                feedback.addClass('text-danger').text('No se encontró esta cédula en Terceros.');
                            } else if (r.ya_tiene_usuario) {
                                feedback.addClass('text-danger').text('✗ ' + r.nombre + ' — ya tiene un usuario con esta cédula.');
                            } else {
                                feedback.addClass('text-success').text('✓ Coincide con: ' + r.nombre);
                                $('#nameInput').val(r.nombre);
                            }
                        })
                        .fail(function () { feedback.removeClass('text-muted').addClass('text-danger').text('No se pudo verificar. Intenta de nuevo.'); });
                }, 400);
            });
            $('#formAddUser').submit(function (event) {
                var form = this;
                if (!form.checkValidity()) {
                    $(form).addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        });
    </script>
</x-base-layout>