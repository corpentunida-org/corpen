<!-- Misma plantilla -->
<h1>Asignar un Rol</h1>
<p>Nombre: {{$user->name}}</p>

 
<!-- Laravel colective -->
{{-- {!! Form::model($user,['route'=> ['admin.users.update',$user], 'method' => 'put']) !!}
    <div>
        <label>
            {!! Form::checkbox('roles[]', $role->id, null)!!}
            {{$role->name}}
        </label>
    </div>
{!! Form::close()!!} --}}
@if (session('info')){
    <div><strong>{{session('info')}}</strong></div>
}

@endif
<h4>Listado de Roles</h4>
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
    <button type="submit" class="btn btn-primary">Asignar Rol</button>
</form>