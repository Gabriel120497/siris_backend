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

//rutas reservas
Route::get('api/reservas', 'ReservaController@reservas');
Route::get('api/reservas/{id}', 'ReservaController@reservasPorId');
Route::post('api/reservas/nueva', 'ReservaController@reservas');

Route::get('api/reservas/?estado', 'ReservaController@reservas');

//rutas equipos
Route::get('api/equipos', 'EquipoController@equipos')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/equipos/{id}', 'EquipoController@equiposPorPlaca')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//rutas instrumentos
Route::get('api/instrumentos', 'InstrumentoController@instrumentos')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/instrumentosDisponibles', 'InstrumentoController@instrumentosDisponibles')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);

//rutas salones
Route::get('api/salones', 'SalonController@salones')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/salones/{id}', 'SalonController@salonPorUbicacion')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);
