<!DOCTYPE html>
<html>
    <head>
        <title>Lista de Usuario</title>
    </head>
    <body>
<h1>Lista de Libros</h1>

<ul>
    @foreach ($users as $user)
        <li>{{$user->title}}        </li>

        @endforeach

</ul>
    </body>
</html>
