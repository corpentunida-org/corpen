<x-base-layout>
    @section('titlepage', 'Home')
    <div class="col-12">
        <div class="welcome-banner mb-4 p-4 p-md-5 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
            <div class="text-center text-md-start">
                <span class="welcome-eyebrow">Bienvenido{{ auth()->user() ? ', ' . explode(' ', auth()->user()->name)[0] : '' }}</span>
                <img src="{{ asset('img/logo.png') }}" alt="Corpentunida" class="welcome-logo">
            </div>
            <div class="welcome-dots" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <style>
        .welcome-banner {
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #eef0f4;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(15, 13, 77, .05);
        }
        .welcome-eyebrow {
            display: block;
            color: #0f0d4d;
            font-weight: 600;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .6rem;
        }
        .welcome-logo {
            width: 260px;
            max-width: 60vw;
        }
        .welcome-dots {
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            width: 160px;
            height: 160px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            opacity: .9;
        }
        .welcome-dots span {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #ffb300;
        }
        .welcome-dots span:nth-child(3n) { background: rgba(255, 179, 0, .35); }
        .welcome-dots span:nth-child(4n) { background: rgba(15, 13, 77, .1); }
        @media (max-width: 767px) {
            .welcome-dots { display: none; }
        }
    </style>
</x-base-layout>
