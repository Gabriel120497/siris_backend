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

    public function nuevoEquipo(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'placa' => 'required|unique:equipos',
                'nombre' => 'required',
                'estado' => 'required',
                'estatus' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $equipo = new Equipo();
                $equipo->placa = $params->placa;
                $equipo->nombre = $params->nombre;
                $equipo->estado = $params->estado;
                $equipo->descripcion_estado = $params->descripcion_estado;
                $equipo->estatus = $params->estatus;
                $equipo->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'equipo' => $equipo
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

    public function equiposPorPlaca($placa) {

        $equipo = Equipo::where('Placa', $placa)->first();
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

    public function deshabilitarEquipo(Request $request) {

        $json = $request->getContent();
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => "Cuerpo Malformado"
        );

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'placa' => 'required',
                'estatus' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            unset($params_array['id']);
            unset($params_array['placa']);
            $equipo = Equipo::where('placa', $params->placa)->first();
            if (!empty($equipo) && is_object($equipo)) {
                $equipo->update($params_array);
                $data = array(
                    'code' => 200,
                    'status' => 'sucess',
                    'equipo' => $equipo
                );
            } else {
                $data['message'] = $data['message'] . ', el equipo no existe';
            }
        }
        return response()->json($data, $data['code']);
    }

}
