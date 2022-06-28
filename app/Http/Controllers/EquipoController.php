<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Equipo;

class EquipoController extends Controller {

    public function equipos() {
        $equipos = Equipo::all();

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'equipos' => $equipos
        ]);
    }

    public function equiposPorPlaca($placa) {

        $equipo = Equipo::where('Placa',$placa)->first();
        if (is_object($equipo)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'equipo' => $equipo
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => "el equipo no existe"
            );
        }
        return response()->json($data, $data['code']);
    }
}
