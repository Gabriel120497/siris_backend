<?php

namespace App\Http\Controllers;

use App\Instrumento;
use App\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstrumentoController extends Controller {

    public function instrumentos() {
        $instrumentos = Instrumento::all();

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'instrumentos' => $instrumentos
        ]);
    }

    public function nuevoInstrumento(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'placa' => 'required|unique:instrumentos',
                'nombre' => 'required',
                'estado' => 'required',
                'estatus' => 'required',
                'trasladable' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $instrumento = new Instrumento();
                $instrumento->placa = $params->placa;
                $instrumento->nombre = $params->nombre;
                $instrumento->estado = $params->estado;
                $instrumento->descripcion_estado = $params->descripcion_estado;
                $instrumento->estatus = $params->estatus;
                $instrumento->trasladable = $params->trasladable;
                $instrumento->id_salon = $params->id_salon;
                $instrumento->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'instrumento' => $instrumento
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado el instrumento, cuerpo malformado'
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function instrumentosDisponibles() {
        $instrumetosLista = Instrumento::select('nombre')->distinct()->where('estatus', 'Disponible')
            ->where('habilitado_para', 'Comunidad')->get();
        $data = array(
            'instrumentos' => $instrumetosLista,
        );
        //var_dump($instrumetosInventario);die();
        return response()->json($data);
    }

    public function deshabilitarInstrumento(Request $request) {

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
            $instrumento = Instrumento::where('placa', $params->placa)->first();
            if (!empty($instrumento) && is_object($instrumento)) {
                $instrumento->update($params_array);
                $data = array(
                    'code' => 200,
                    'status' => 'sucess',
                    'instrumento' => $instrumento,
                    'cambios' => $params_array
                );
            }
        }
        return response()->json($data, $data['code']);
    }
}
