<?php

namespace App\Http\Controllers;

use App\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller {

    public $key;

    public function __construct() {
        $this->key = 'C9fBxl1EWtYTL1/M8jfstw==';
    }

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
            $pwd = JWT::encode($params->clave, $this->key, 'HS256');
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
                $enviar_correo = false;
                if (empty($params->clave)) {
                    $pwd = str_random(8);
                    $enviar_correo = true;
                } else {
                    $pwd = $params->clave;
                }

                $jwt = JWT::encode($pwd, $this->key, 'HS256');
                $usuario = new Usuario();
                $usuario->rol = $params->rol;
                $usuario->nombre = $params->nombre;
                $usuario->apellido = $params->apellido;
                $usuario->tipo_documento = $params->tipo_documento;
                $usuario->numero_documento = $params->numero_documento;
                $usuario->celular = $params->celular;
                $usuario->correo = $params->correo;
                $usuario->clave = $jwt;
                $usuario->save();

                if ($enviar_correo) {
                    $this->enviarCorreoPwd($usuario);
                }

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

    public function enviarCorreoPwd($usuario) {
        $clave = JWT::decode($usuario->clave, $this->key, ['HS256']);
        $usuario->clave = $clave;
        $array_usuario = json_decode($usuario, true);
        //var_dump($array_reserva);die();
        Mail::send('email.nuevoColaborador', $array_usuario, function ($msj) use ($usuario) {
            $msj->from("kaizer450450@gmail.com", "Registro Usuario Fomento Poli");
            $msj->subject('Información de su perfil');
            $msj->to($usuario->correo);
        });
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
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No hay profesores'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function colaboradores() {
        $colaboradores = Usuario::where('rol', '!=', 'Comunidad')->where('rol', '!=', 'Externo')
            ->select('id', 'nombre', 'apellido', 'celular', 'correo', 'rol')
            ->get();
        /*dd($profesores);
        die();*/
        if (!empty($colaboradores)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'colaboradores' => $colaboradores
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No hay colaboradores'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function editarColaborador(Request $request) {
        $json = $request->getContent();
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => "No se pudo editar el colaborador"
        );

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'id' => 'required',
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            $colaborador = Usuario::find($params->id);
            //dd($colaborador);die();
            unset($params_array['id']);
            if (!empty($colaborador) && is_object($colaborador)) {
                $colaborador->update($params_array);
                $data = array(
                    'code' => 200,
                    'status' => 'sucess',
                    'colaborador' => $colaborador,
                    'cambios' => $params_array
                );
            }else{
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => "No se encontró el colaborador"
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    public function eliminarColaborador($id_colaborador) {
        $colaborador = Usuario::find($id_colaborador);
        if (!empty($colaborador)) {
            $colaborador->delete();
            $data = [
                'code' => 200,
                'status' => 'success',
                'colaborador' => $colaborador
            ];

        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }
}
