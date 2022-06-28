<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller {

    public function login(Request $request) {
        $jwtAuth = new \JwtAuth();
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //var_dump($json);die();
        $validate = \Validator::make($params_array, [
            'correo' => 'required|email',
            'clave' => 'required'
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'errors' => $validate->errors()
            );
        } else {
            $pwd = hash('sha256', $params->clave);
            $signup = $jwtAuth->signup($params->correo, $pwd);
            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->correo, $pwd, true);
            }
        }
        return response()->json($signup, 200);
    }

    public function update(Request $request) {
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        if ($checkToken) {
            $json = $request->input('json', null);
            $param_array = json_decode($json, true);//array
            $user = $jwtAuth->checkToken($token, true);

            //validar datos
            $validate = \Validator::make($param_array, [
                'nombre' => 'required|alpha',
                'correo' => 'required|email|unique:users' . $user->sub,
                'rol' => 'required'
            ]);
        } else {
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'El usuario no se ha identificado correctamente'
            );
        }
        return response()->json($data, $data['code']);
    }
}
