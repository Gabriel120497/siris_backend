<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use App\Salon;

class SalonController extends Controller {

    public function salones() {
        $salones = Salon::all();

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'salones' => $salones
        ]);
    }

    public function nuevoSalon(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'ubicacion' => 'required|unique:salones'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $salon = new Salon();
                $salon->ubicacion = $params->ubicacion;
                $salon->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'salon' => $salon
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado el equipo, cuerpo malformado'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function salonPorUbicacion($ubicacion) {

        $salon = Salon::where('ubicacion', $ubicacion)->first();

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

    public function eliminarSalon($id_salon) {
        $salon = Salon::find($id_salon);
        //dd($salon);
        if (!empty($salon)) {
            try {
                $salon->delete();
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'salon' => $salon
                ];
            } catch (QueryException $exception) {
                $data = [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'el salón no se puede eliminar, está reservado o se usa para una clase'
                ];
            }
            /*var_dump($salon);
            die();*/
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El salón no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
