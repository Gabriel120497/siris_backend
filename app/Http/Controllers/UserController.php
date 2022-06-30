<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                'correo' => 'required|email' . $user->sub,
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

    public function nuevoUsuario(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'rol' => 'required',
                'nombre' => 'required',
                'apellido' => 'required',
                'tipo_documento' => 'required',
                'numero_documento' => 'required|unique:usuarios',
                'celular' => 'required',
                'correo' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $pwd = str_random(8);
                $usuario = new Usuario();
                $usuario->rol = $params->rol;
                $usuario->nombre = $params->nombre;
                $usuario->apellido = $params->apellido;
                $usuario->tipo_documento = $params->tipo_documento;
                $usuario->numero_documento = $params->numero_documento;
                $usuario->celular = $params->celular;
                $usuario->correo = $params->correo;
                $usuario->clave = openssl_encrypt($pwd, 'aes-256-cbc', 'jTl7WNRV', false, base64_decode("C9fBxl1EWtYTL1/M8jfstw=="));
                $usuario->save();
                unset($usuario->clave);
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'usuario' => $usuario,
                    'borrar' => $pwd
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado el usuario. Faltan datos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function enviarCorreoPwd(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'tipo_documento' => 'required',
                'numero_documento' => 'required',
                'correo' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $usuario = Usuario::where('usuarios.tipo_documento', '=', $params_array['tipo_documento'])
                    ->where('usuarios.numero_documento', '=', $params_array['numero_documento'])
                    ->where('usuarios.correo', '=', $params_array['correo'])->select('clave')->get();
                //print_r($usuario);
                //die();
                if (!empty($usuario)) {
                    $clave = openssl_decrypt($usuario[0]->clave, 'aes-256-cbc', 'jTl7WNRV', false, base64_decode("C9fBxl1EWtYTL1/M8jfstw=="));
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'clave' => $clave
                    ];
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'El usuario no existe'
                    ];
                }
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado el usuario. Faltan datos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function profesores() {
        $profesores = Usuario::where('rol', '=', 'Profesor')
            ->select('id', 'nombre', 'apellido', 'celular', 'correo')
            ->get();
        /*dd($profesores);
        die();*/
        if (!empty($profesores)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'profesores' => $profesores
            );
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No hay profesores'
            );
        }
        return response()->json($data);
    }
}
