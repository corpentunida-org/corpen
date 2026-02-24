<x-base-layout>
    @section('titlepage', 'Agregar un inmueble')
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}.
            @endforeach
        </div>
    @endif
    <style>
        .preview-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
        }

        .preview-wrapper {
            position: relative;
        }

        .btn-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            cursor: pointer;
        }

        #dropZone {
            cursor: pointer;
            transition: .2s;
        }

        #dropZone.dragover {
            background: #e9f5ff;
            border-color: #0d6efd;
        }
    </style>

    <div class="card">
        <div class="card-body pass-info">
            <form id="formAddInmueble" action={{ route('reserva.crudinmuebles.store') }} method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Crear un nuevo inmueble:</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                            Registra los datos del inmueble que será asociado a la reserva. Asegúrate de ingresar la
                            información correctamente.
                        </span>
                    </h5>
                    <a href="#" class="btn btn-sm btn-light-brand">
                        <i class="bi bi-buildings me-1"></i> Ver inmuebles
                    </a>
                </div>

                <!-- NOMBRE -->
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="nombreInmueble" class="fw-semibold">Nombre:</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-house-door"></i>
                            </span>
                            <input type="text" class="form-control" id="nombreInmueble" name="nombre"
                                placeholder="Nombre del inmueble" required>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="descripcionInmueble" class="fw-semibold">Descripción:</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <textarea class="form-control" id="descripcionInmueble" name="descripcion" rows="3"
                                placeholder="Descripción del inmueble" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="direccionInmueble" class="fw-semibold">Dirección:</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" class="form-control" id="direccionInmueble" name="direccion"
                                placeholder="Dirección completa" required>
                        </div>
                    </div>
                </div>

                <!-- CIUDAD -->
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="ciudadInmueble" class="fw-semibold">Ciudad:</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" class="form-control" id="ciudadInmueble" name="ciudad"
                                placeholder="Ciudad" required>
                        </div>
                    </div>
                </div>

                <!-- GOOGLE MAPS -->
                <div class="row mb-4 align-items-center">
                    <div class="col-lg-4">
                        <label for="mapsLink" class="fw-semibold">Ubicación en Google Maps:</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-map"></i>
                            </span>
                            <input type="url" class="form-control" id="mapsLink" name="maps"
                                placeholder="Pega aquí el link de Google Maps" required>
                        </div>
                        <small class="text-muted">
                            Copia el enlace desde Google Maps → Compartir → Copiar vínculo.
                        </small>
                    </div>
                </div>
                <!-- FOTOS -->
                <div class="row mb-2 align-items-center">
                    <div class="col-lg-4">
                        <label class="fw-semibold">Fotos del inmueble:</label>
                    </div>

                    <div class="col-lg-8">
                        <div id="dropZone" class="border rounded p-2 text-center bg-light position-relative">
                            <i class="bi bi-cloud-upload text-muted"></i>
                            <p class="m-0">Arrastra imágenes aquí</p>
                            <small class="text-muted">o haz click para seleccionar</small>
                            <input type="file" id="imagenesInput" name="imagenes[]" multiple accept="image/*" hidden>
                        </div>

                        <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-3"></div>

                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success"><i class="bi bi-plus me-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (() => {
            const form = document.getElementById('formAddInmueble');
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        })();

        const dropZone = document.getElementById('dropZone');
        const input = document.getElementById('imagenesInput');
        const preview = document.getElementById('previewContainer');

        let filesArray = [];

        /* abrir selector */
        dropZone.addEventListener('click', () => input.click());

        /* seleccionar archivos */
        input.addEventListener('change', e => {
            handleFiles(e.target.files);
        });

        /* drag events */
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        function handleFiles(files) {
            [...files].forEach(file => {
                if (!file.type.startsWith('image/')) return;

                filesArray.push(file);
                renderPreview(file);
            });

            updateInputFiles();
        }

        function renderPreview(file) {
            const reader = new FileReader();

            reader.onload = e => {
                const wrapper = document.createElement('div');
                wrapper.className = 'preview-wrapper';

                wrapper.innerHTML = `
            <img src="${e.target.result}" class="preview-img">
            <button type="button" class="btn-remove">&times;</button>
        `;

                wrapper.querySelector('button').onclick = () => {
                    filesArray = filesArray.filter(f => f !== file);
                    wrapper.remove();
                    updateInputFiles();
                };

                preview.appendChild(wrapper);
            };

            reader.readAsDataURL(file);
        }

        function updateInputFiles() {
            const dt = new DataTransfer();
            filesArray.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }
    </script>

</x-base-layout>
