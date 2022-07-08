<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
</head>

<body>
<div>Información :
    <ol>
        <li>Correo: {!! $correo !!}</li>
        <li>Clave: {!! $clave !!}</li>
        <li>Perfil : {!! $rol !!}</li>
    </ol>
</div>
</body>
</html>
