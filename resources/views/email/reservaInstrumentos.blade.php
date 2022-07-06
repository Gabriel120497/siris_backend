<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
</head>

<body>
<h2>Correo estado reserva</h2>
<div>Su reserva :
    <ol>
        <li>Id: {!! $id !!}</li>
        <li>fecha y hora inicial : {!! $fecha_inicio !!}</li>
        <li>fecha y hora final : {!! $fecha_fin !!}</li>
        <li>Estado : {!! $estado !!}</li>
    </ol>
</div>
</body>
</html>
