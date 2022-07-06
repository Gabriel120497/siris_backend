<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('usuario/pruebas', 'UserController@prueba');

//rutas usuario
Route::post('api/login', 'UserController@login');
Route::post('api/usuario/update', 'UserController@update');
Route::post('api/usuario/nuevoUsuario', 'UserController@nuevoUsuario');
Route::post('api/usuario/enviarCorreoPwd', 'UserController@enviarCorreoPwd');
Route::get('api/usuario/profesores', 'UserController@profesores');

//rutas reservas
Route::get('api/reservas', 'ReservaController@reservas');
Route::get('api/reservas/{id}', 'ReservaController@reservasPorId');
Route::post('api/nuevaReserva', 'ReservaController@nuevaReserva');
//Route::post('api/correo', 'ReservaController@enviarCorreoReserva');

Route::get('api/reservas/{estado}', 'ReservaController@reservas');

//rutas equipos
Route::get('api/equipos', 'EquipoController@equipos');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/equipos/{placa}', 'EquipoController@equiposPorPlaca')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/nuevoEquipo', 'EquipoController@nuevoEquipo');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('api/deshabilitarEquipo', 'EquipoController@deshabilitarEquipo');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//rutas instrumentos
Route::get('api/instrumentos', 'InstrumentoController@instrumentos');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/instrumentosComunidad', 'InstrumentoController@instrumentosComunidad');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/instrumentosDisponibles', 'InstrumentoController@instrumentosDisponibles');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('api/deshabilitarInstrumento', 'InstrumentoController@deshabilitarInstrumento');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/nuevoInstrumento', 'InstrumentoController@nuevoInstrumento');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('api/actualizarInstrumento', 'InstrumentoController@actualizarInstrumento');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//rutas salones
Route::get('api/salones', 'SalonController@salones');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/salones/{id}', 'SalonController@salonPorUbicacion')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/nuevoSalon', 'SalonController@nuevoSalon');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//Grupos
Route::get('api/grupos', 'GrupoController@grupos');
Route::post('api/nuevoGrupo', 'GrupoController@nuevoGrupo');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/estudiantes/{idGrupo}', 'GrupoController@estudiantes');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/misGrupos/{profesor}', 'GrupoController@misGrupos');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::delete('api/eliminarGrupo/{id_grupo}', 'GrupoController@eliminarGrupo');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('api/actualizarGrupo', 'GrupoController@actualizarGrupo');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//Audiciones
Route::get('api/audiciones', 'GrupoUsuarioController@audiciones');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/audicionesPendientes', 'GrupoUsuarioController@audicionesPendientes');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/nuevaAudicion', 'GrupoUsuarioController@nuevaAudicion');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::put('api/actualizarAudicion', 'GrupoUsuarioController@actualizarAudicion');//->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
