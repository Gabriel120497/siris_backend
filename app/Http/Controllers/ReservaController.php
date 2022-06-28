<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Reserva;

class ReservaController extends Controller {

    public function reservas() {
        $reservas = Reserva::all();

        if (is_object($reservas)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'reservas' => $reservas
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontró ninguna reserva'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function reservasPorId($id) {

        $reservas = Reserva::where('id', $id)->first();

        if (is_object($reservas)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'reserva' => $reservas
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => "No existe la reserva con id: $id"
            );
        }
        return response()->json($data, $data['code']);
    }
}
