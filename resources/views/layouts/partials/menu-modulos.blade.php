{{-- El menú se arma por PERMISOS, no por nombre de rol: cada archivo de
     layouts/actions/<modulo>.blade.php se muestra si el perfil de la persona tiene el permiso
     `menu.<modulo>` (se marca en la Matriz de Permisos). Ver la guía: route('admin.guia.permisos'). --}}
@php
    $permisosDeMenu = auth()->user()->getDirectPermissions()->pluck('name')->flip();
    $modulosMenu = collect(\Illuminate\Support\Facades\File::files(resource_path('views/layouts/actions')))
        ->map(fn ($f) => str_replace('.blade.php', '', $f->getFilename()))
        ->sort();
@endphp
@foreach ($modulosMenu as $modulo)
    @if ($permisosDeMenu->has('menu.' . $modulo))
        @include('layouts.actions.' . $modulo)
    @endif
@endforeach
