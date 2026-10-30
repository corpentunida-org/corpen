<!DOCTYPE html>
<html>
    <head>
        <title>Lista de Libros  </title>
    </head>
    <body>
        <h1>Lista de Libros</h1>

        <ul>
            @foreach ($books as $book)
            <li>{{$book->tittle}}</li>
            @endforeach
        </ul>
    </body>
</html>
