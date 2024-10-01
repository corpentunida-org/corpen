
<!-- Laravel colective -->
{{-- {!! Form::model($user,['route'=> ['admin.users.update',$user], 'method' => 'put']) !!}
    <div>
        <label>
            {!! Form::checkbox('roles[]', $role->id, null)!!}
            {{$role->name}}
        </label>
    </div>
{!! Form::close()!!} --}}


@extends('layouts.appTemplate')
@section('titlepage', 'asignar rol')
@section('titleView', 'Administracion Usuarios')
@section('titlenav', 'ADMINISTRACION')
@section('contentpage')
    <div class="card-header">
        <h5 class="card-category">Asignar rol</h5>
    </div>
    <div class="card-body">
    @if (session('info')){
        <div><strong>{{session('info')}}</strong></div>
    }
    @endif
    <h5>Usuario: {{$user->name}}</h5>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @foreach($roles as $role)
            <div>
                <label>
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            </div>
        @endforeach
        <button type="submit" class="btn btn-warning">Asignar Rol</button>
    </form>
    </div>
    
@endsection