<?php

namespace App\Http\Controllers;

use App\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller {

    public function grupos() {

        $grupos = Grupo::join('salones', 'salones.id', '=', 'grupos.id_salon_clases')
            ->select('grupos.id', 'grupos.nombre', 'grupos.descripcion', 'grupos.profesor', 'grupos.cupos_totales',
                'grupos.cupos_restantes', 'grupos.prerequisitos', 'grupos.horario', 'salones.ubicacion', 'salones.id as id_salon')->get();
        if (!empty($grupos)) {
            foreach ($grupos as &$grupo) {
                $grupo->horario = json_decode($grupo->horario);
            }
        }
        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'grupos' => $grupos
        ]);
    }

    public function nuevoGrupo(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'nombre' => 'required|unique:grupos',
                'profesor' => 'required',
                'cupos_totales' => 'required',
                'cupos_restantes' => 'required',
                'horario' => 'required',
                'id_salon_clases' => 'required'

            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $horario = $params->horario;
                $grupo = new Grupo();
                $grupo->nombre = $params->nombre;
                $grupo->descripcion = $params->descripcion;
                $grupo->profesor = $params->profesor;
                $grupo->cupos_totales = $params->cupos_totales;
                $grupo->cupos_restantes = $params->cupos_restantes;
                $grupo->horario = json_encode($horario, true);
                $grupo->id_salon_clases = $params->id_salon_clases;
                $grupo->save();
                $grupo->horario = json_decode($grupo->horario);
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'grupo' => $grupo
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado el grupo, cuerpo malformado'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function estudiantes($idGrupo) {
        $estudiantes = Grupo::join('grupos_usuarios', 'grupos_usuarios.id_grupo', '=', 'grupos.id')
            ->join('usuarios', 'grupos_usuarios.id_usuario', '=', 'usuarios.id')
            ->select('usuarios.nombre', 'usuarios.apellido', 'usuarios.tipo_documento', 'usuarios.numero_documento',
                'usuarios.correo', 'usuarios.celular')
            ->where('grupos_usuarios.estado_usuario', '=', 'Integrante')
            ->where('grupos.id', '=', $idGrupo)->get();

        if (count($estudiantes)) {
            $data = [
                'code' => 200,
                'status' => 'sucess',
                'estudiantes' => $estudiantes
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El grupo aún no tiene estudiantes'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function misGrupos($profesor) {
        $mis_grupos = Grupo::join('salones', 'salones.id', '=', 'grupos.id_salon_clases')
            ->select('grupos.id', 'grupos.nombre', 'grupos.descripcion', 'grupos.cupos_totales',
                'grupos.cupos_restantes', 'grupos.prerequisitos', 'grupos.horario', 'salones.ubicacion')
            ->where('grupos.profesor', '=', $profesor)->get();

        if (count($mis_grupos)) {
            foreach ($mis_grupos as &$grupo) {
                $grupo->horario = json_decode($grupo->horario);
            }
            $data = [
                'code' => 200,
                'status' => 'sucess',
                'grupos' => $mis_grupos
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No hay grupos'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function eliminarGrupo($id_grupo) {
        $grupo = Grupo::find($id_grupo);
        if (!empty($grupo)) {
            $grupo->delete();
            $data = [
                'code' => 200,
                'status' => 'success',
                'grupo' => $grupo
            ];

        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El grupo no existe'
            ];
        }


        return response()->json($data, $data['code']);
    }

    public function actualizarGrupo(Request $request) {

        $json = $request->getContent();
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => "No se pudo actualizar el grupo de proyección"
        );

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'id' => 'required',
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            unset($params_array['id']);
            $grupo = Grupo::where('id', $params->id)->first();
            if (!empty($grupo) && is_object($grupo)) {
                $grupo->update($params_array);
                $data = array(
                    'code' => 200,
                    'status' => 'sucess',
                    'grupo' => $grupo,
                    'cambios' => $params_array
                );
            }
        }
        return response()->json($data, $data['code']);
    }



//TODO servicio que me devuelva la cantidad de cupos totales y restantes de un grupo
    public
    function cuposDeUnGrupo() {
        $grupos = Grupo::all();

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'audiciones' => $grupos
        ]);
    }

}
