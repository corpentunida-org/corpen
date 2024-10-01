<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista del Admin</title>
</head>
<body>
    <h1>Lista de usuarios</h1>
    <table>
        @foreach ( $users as $user )
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>
                    <a href="{{route('admin.users.edit', $user)}}">Editar</a>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html> -->
@extends('layouts.appTemplate')
@section('titlepage', 'index user')
@section('titleView', 'Administracion Usuarios')
@section('titlenav', 'ADMINISTRACION')
@section('contentpage')
    <div class="card-header">
        <h5 class="card-category">Lista de usuarios</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                @foreach ($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>
                        <a href="{{route('admin.users.edit', $user)}}" class="btn btn-warning p-2">Editar</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection