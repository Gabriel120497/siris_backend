<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Salon;

class SalonController extends Controller {

    public function salones(){
        $salones=Salon::all();

        return response()->json([
            'code' => 200,
            'status'=> 'sucess',
            'salones'=> $salones
        ]);
    }

    public function salonPorUbicacion($ubicacion) {

        $salon = Salon::where('ubicacion',$ubicacion)->first();

        if (is_object($salon)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'equipo' => $salon
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => "el salón no existe"
            );
        }
        return response()->json($data, $data['code']);
    }
}
