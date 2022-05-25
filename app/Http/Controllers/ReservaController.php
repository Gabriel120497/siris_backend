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

    public function reservasPorId($id, $tipo) {

        switch ($tipo) {
            case 'Instrumento':
                $reservas = Reserva::where('id_instrumento', $id)->get();
                break;
            case 'Salon':
                $reservas = Reserva::where('id_salon', $id)->get();
                break;
            case 'Equipo':
                $reservas = Reserva::where('id_equipo', $id)->get();
                break;
        }

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
                'message' => "el $tipo no tiene reservas"
            );
        }
        return response()->json($data, $data['code']);
    }
}
