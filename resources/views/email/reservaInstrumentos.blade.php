<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
</head>

<body>
<div>Su reserva :
    <ol>
        <li>Id: {!! $id !!}</li>
        <li>{!! $tipo_item !!}: {!! $item !!}</li>
        <li>fecha y hora inicial (AAAA-MM-DD): {!! $fecha_inicio !!}</li>
        <li>fecha y hora final (AAAA-MM-DD): {!! $fecha_fin !!}</li>
        <li>Estado : {!! $estado !!}</li>
    </ol>
</div>
</body>
</html>
