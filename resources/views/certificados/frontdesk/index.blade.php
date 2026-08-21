<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        
        .btn-pastel-primary {
            background-color: #4a90e2;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-pastel-primary:hover {
            background-color: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
            color: white;
        }
        
        .search-minimal {
            background-color: #f8f9fa;
            border: 1px solid #ececec;
            border-radius: 12px;
            padding: 15px 20px;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        .search-minimal:focus {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #4a90e2;
            outline: none;
        }
    </style>

    <div class="app-container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            
            <div class="text-center mb-5">
                <div class="symbol-label bg-pastel-primary mx-auto mb-4" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 25px;">
                    <i class="fas fa-fingerprint text-primary fs-1"></i>
                </div>
                <h1 class="h2 fw-bold text-dark" style="letter-spacing: -0.5px;">Portal de Clientes</h1>
                <p class="text-muted fs-6">Consulte el estado de sus operaciones y cartera</p>
            </div>

            {{-- Manejo de Errores --}}
            @if(session('error'))
                <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show rounded-4 px-4 py-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 alert-dismissible fade show rounded-4 px-4 py-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card card-custom shadow-sm border-0 overflow-hidden">
                <div class="card-body p-5">
                    <form action="{{ route('certificados.frontdesk.autenticar') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="cod_ter" class="form-label fw-bold text-uppercase fs-8 text-muted mb-3">Número de Identificación (NIT/CC)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 position-absolute" style="z-index: 10; left: 10px; top: 15px;">
                                    <i class="fas fa-search text-muted opacity-50"></i>
                                </span>
                                <input type="text" class="form-control search-minimal ps-5 w-100" id="cod_ter" name="cod_ter" placeholder="Ej. 900123456" required autocomplete="off">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-pastel-primary btn-lg w-100 shadow-sm fw-bold rounded-pill mt-2 py-3">
                            Ingresar al Portal <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-base-layout>