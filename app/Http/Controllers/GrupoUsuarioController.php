<?php

namespace App\Http\Controllers;

use App\Grupo;
use App\Grupo_Usuario;
use Illuminate\Http\Request;

class GrupoUsuarioController extends Controller {

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
                'grupos.nombre as grupo', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.correo',
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
                'id_usuario' => 'required',
                'id_grupo' => 'required',
                'estado_usuario' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $audicion = new Grupo_Usuario();
                $audicion->id_usuario = $params->id_usuario;
                $audicion->id_grupo = $params->id_grupo;
                $audicion->estado_usuario = $params->estado_usuario;
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
                'id_grupo' => 'required',
                'id_usuario' => 'required',
                'estado_usuario' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            unset($params_array['solicitud']);
            unset($params_array['id_usuario']);
            unset($params_array['id_grupo']);
            $audicion = Grupo_Usuario::where('id', $params->solicitud)
                ->where('grupos_usuarios.id_grupo', $params->id_grupo)
                ->first();
            if (!empty($audicion) && is_object($audicion)) {
                $audicion->update($params_array);
                if ($params->estado_usuario == 'Integrante') {
                    $grupo = Grupo::where('id', $params->id_grupo)->first();
                    if ($grupo->cupos_restantes > 0) {
                        $grupoArray = [
                            'cupos_restantes' => $grupo->cupos_restantes - 1
                        ];
                        $grupo->update($grupoArray);
                        $data = array(
                            'code' => 200,
                            'status' => 'sucess',
                            'audicion' => $grupo
                        );
                    } else {
                        $data['message'] = 'El grupo no tiene mas cupos disponibles';
                    }

                }
            } else {
                $data['message'] = $data['message'] . ', el usuario no ha audicionado para este grupo';
            }
        }
        return response()->json($data, $data['code']);
    }
}
