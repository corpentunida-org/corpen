<x-base-layout>
    <div class="app-container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="fw-bolder m-0 text-dark fs-2">Importar Extracto</h3>
                        <p class="text-muted m-0 fs-6">Sube el archivo Excel o CSV generado por el banco</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="card-body p-5">
                        <form action="{{ route('contabilidad.extractos.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bolder text-dark fs-5">Selecciona la Cuenta Bancaria</label>
                                <select name="id_con_cuentas_bancaria" class="form-select form-select-lg rounded-pill border-light bg-light fs-5" required>
                                    <option value="" disabled selected>Elige una cuenta activa...</option>
                                    @foreach($cuentas as $cuenta)
                                        <option value="{{ $cuenta->id }}">{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bolder text-dark fs-5">Archivo del Banco</label>
                                <div class="border border-2 border-dashed border-primary rounded-3 text-center p-5 bg-light-primary position-relative" style="transition: all 0.3s;">
                                    {{-- AQUÍ SE HABILITAN LOS FORMATOS EXCEL (.xls, .xlsx) --}}
                                    <input type="file" name="archivo_extracto" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv, .txt, .xls, .xlsx" required>
                                    
                                    <i class="fas fa-file-excel fa-3x text-primary mb-3"></i>
                                    <h5 class="fw-bolder text-dark">Haz clic o arrastra tu archivo Excel/CSV aquí</h5>
                                    <p class="text-muted mb-1 fs-6">Formatos soportados: Excel (.xlsx, .xls) y CSV</p>
                                    <span class="badge bg-light-info text-info fs-8 mt-2">
                                        Estructura esperada: Fecha | Desc. | Hash | Valor | Cédula | Nombre | Oficina | Distrito
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-light rounded-pill fs-5 px-5 py-3 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary rounded-pill fs-5 px-5 py-3 fw-bolder shadow-sm">
                                    <i class="fas fa-eye me-2"></i> Previsualizar Archivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>