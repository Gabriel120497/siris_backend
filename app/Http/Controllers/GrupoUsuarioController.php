<?php

namespace App\Http\Controllers;

use App\Grupo;
use App\Grupo_Usuario;
use App\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class GrupoUsuarioController extends Controller {

    public $key;

    public function __construct() {
        $this->key = 'C9fBxl1EWtYTL1/M8jfstw==';
    }

    public function audiciones() {
        $audiciones = Grupo_Usuario::join('grupos', 'grupos_usuarios.id_grupo', '=', 'grupos.id')
            ->join('usuarios', 'grupos_usuarios.id_usuario', '=', 'usuarios.id')
            ->select('grupos_usuarios.id', 'grupos_usuarios.estado_usuario',
                'grupos.nombre as grupo', 'usuarios.nombre', 'usuarios.correo',
                'usuarios.celular');

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'audiciones' => $audiciones
        ]);
    }

    public function audicionesPendientes(Request $request) {
        $profesor = $request->nombre;
        $audicionesPendientes = Grupo_Usuario::join('grupos', 'grupos_usuarios.id_grupo', '=', 'grupos.id')
            ->join('usuarios', 'grupos_usuarios.id_usuario', '=', 'usuarios.id')
            ->select('grupos_usuarios.id', 'grupos_usuarios.estado_usuario',
                'grupos.nombre as grupo', 'grupos_usuarios.id_grupo', 'usuarios.id as id_usuario', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.correo',
                'usuarios.celular')
            ->where('grupos_usuarios.estado_usuario', '=', 'Pendiente')
            ->where('grupos.profesor', '=', $profesor)->get();

        if (!empty($audicionesPendientes) && count($audicionesPendientes)) {
            $data = [
                'code' => 200,
                'status' => 'sucess',
                'audiciones' => $audicionesPendientes
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No hay audiciones pendientes'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function nuevaAudicion(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'tipo_documento' => 'required',
                'numero_documento' => 'required',
                'id_grupo' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {

                $usuario = Usuario::where('tipo_documento', $params->tipo_documento)
                    ->where('numero_documento', $params->numero_documento)->first();

                $usuario_nuevo = '';
                if (empty($usuario)) {
                    $request_nuevo = [
                        'rol' => 'Externo',
                        'nombre' => $params->nombre,
                        'apellido' => $params->apellido,
                        'tipo_documento' => $params->tipo_documento,
                        'numero_documento' => $params->numero_documento,
                        'celular' => $params->celular,
                        'correo' => $params->correo
                    ];
                    $usuario_nuevo = $this->nuevoUsuario($request_nuevo);//TODO Crear el usuario
                }

                $usuario = Usuario::where('tipo_documento', $params->tipo_documento)
                    ->where('numero_documento', $params->numero_documento)->first();
                $audicion = new Grupo_Usuario();
                $audicion->id_usuario = $usuario->id;
                $audicion->id_grupo = $params->id_grupo;
                $audicion->estado_usuario = 'PENDIENTE';
                $audicion->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'audicion' => $audicion
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado la audición, cuerpo malformado'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function actualizarAudicion(Request $request) {

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
                'solicitud' => 'required',
                'estado_usuario' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            unset($params_array['solicitud']);
            $audicion = Grupo_Usuario::find($params->solicitud);
            if (!empty($audicion) && is_object($audicion)) {
                $audicion->update($params_array);
                if ($params->estado_usuario == 'INTEGRANTE') {
                    $grupo = Grupo::where('id', $params->id_grupo)->first();
                    if ($grupo->cupos_restantes > 0) {
                        $grupoArray = [
                            'cupos_restantes' => $grupo->cupos_restantes - 1
                        ];
                        $grupo->update($grupoArray);
                    } else {
                        $data['message'] = 'El grupo no tiene mas cupos disponibles';
                    }/* else if ($params->estado_usuario == 'RECHAZADO') {

                        //TODO Eliminar el usuario si no pertenece a ningun otro grupo y el perfil es externo
                        $grupo->update($grupoArray);
                        $data = array(
                            'code' => 200,
                            'status' => 'sucess',
                            'audicion' => $grupo
                        );
                    }*/


                }
                $data = array(
                    'code' => 200,
                    'status' => 'sucess',
                    'audicion' => 'Se ha actualizado con éxito'
                );
            } else {
                $data['message'] = 'El usuario no ha audicionado para este grupo';
            }
        }
        return response()->json($data, $data['code']);
    }

    public function nuevoUsuario($request) {
        //$json = $request->getContent();

        //$params = json_decode($request);
        //$params_array = json_decode($request, true);

        if (!empty($request)) {
            $validate = \Validator::make($request, [
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
                if (empty($request->clave)) {
                    $pwd = str_random(8);
                    $enviar_correo = true;
                } else {
                    $pwd = $request->clave;
                }

                $jwt = JWT::encode($pwd, $this->key, 'HS256');
                $usuario = new Usuario();
                $usuario->rol = $request['rol'];
                $usuario->nombre = $request['nombre'];
                $usuario->apellido = $request['apellido'];
                $usuario->tipo_documento = $request['tipo_documento'];
                $usuario->numero_documento = $request['numero_documento'];
                $usuario->celular = $request['celular'];
                $usuario->correo = $request['correo'];
                $usuario->clave = $jwt;
                $usuario->save();

                if ($enviar_correo) {
                    $correo = new UserController();
                    $correo->enviarCorreoPwd($usuario);
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
        return $usuario;
    }

}
