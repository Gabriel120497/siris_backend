<?php

namespace App\Http\Controllers;

use App\Instrumento;
use App\Salon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Reserva;
use Illuminate\Support\Facades\Mail;

class ReservaController extends Controller {

    public function reservas() {
        $reservas = Reserva::all();

        if (is_object($reservas)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'reservas' => $reservas
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontró ninguna reserva'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function reservasPorId($id) {

        $reservas = Reserva::where('id', $id)->first();

        if (is_object($reservas)) {
            $data = array(
                'code' => 200,
                'status' => 'sucess',
                'reserva' => $reservas
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => "No existe la reserva con id: $id"
            );
        }
        return response()->json($data, $data['code']);
    }

    public function nuevaReserva(Request $request) {
        $json = $request->getContent();

        $params = json_decode($json);
        $params_array = json_decode($json, true);
        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [
                'id_usuario' => 'required',
                'item' => 'required',
                'tipo_item' => 'required',
                'estado' => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => $validate->errors()
                ];
            } else {
                $idItemReq = $this->idItem($params->item, $params->fecha_inicio, $params->fecha_fin, $params->tipo_item);
                if (count($idItemReq)) {
                    $reserva = new Reserva();
                    if ($params->tipo_item == 'Instrumentos') {
                        $reserva->id_instrumento = $idItemReq[0]->id;
                    } elseif ($params->tipo_item == 'Equipos') {
                        $reserva->id_equipo = $idItemReq[0]->id;
                    } else {
                        $reserva->id_salon = $idItemReq[0]->id;
                    }
                    $reserva->id_usuario = $params->id_usuario;
                    $reserva->estado = $params->estado;
                    $reserva->fecha_inicio = $params->fecha_inicio;
                    $reserva->fecha_fin = $params->fecha_fin;
                    $reserva->save();
                    $idItemReq = null;
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'reserva' => $reserva
                    ];
                    $this->enviarCorreoReserva($reserva);
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No hay elementos diponibles en este horario'
                    ];
                }
                //var_dump($idItemReq);die();
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha creado la reserva'
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function enviarCorreoReserva($reserva){
        $array_reserva = json_decode($reserva,true);
        Mail::send('email.reservaInstrumentos',$array_reserva, function($msj) {
            $msj->from("kaizer450450@gmail.com","Reservas Poli");
            $msj->subject('Correo Prueba');
            $msj->to('gabrieljaime09@gmail.com');
        });
    }

    private function idItem($item, $fecha_inicio, $fecha_fin, $tipo_item) {

        if ($tipo_item == 'Instrumentos') {
            $idItem = Instrumento::select('id')->where('estatus', 'Disponible')
                ->where('habilitado_para', 'Comunidad')
                ->whereNotExists(function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->select('*')
                        ->from('reservas')
                        ->whereRaw('instrumentos.id = reservas.id_instrumento')
                        ->where('reservas.fecha_inicio', $fecha_inicio)
                        ->where('reservas.fecha_fin', $fecha_fin);
                })->where('nombre', $item)->take(1)->get();
        } elseif ($tipo_item == 'Equipos') {
            //$reserva->id_equipo = $idItemReq[0]->id;
        } else {
            $idItem = Salon::select('id')->where('estatus', 'Disponible')
                ->whereNotExists(function ($query) use ($fecha_inicio, $fecha_fin) {
                    $query->select('*')
                        ->from('reservas')
                        ->whereRaw('salones.id = reservas.id_salon')
                        ->where('reservas.fecha_inicio', $fecha_inicio)
                        ->where('reservas.fecha_fin', $fecha_fin);
                })->where('ubicacion', $item)->take(1)->get();
            //var_dump($idItem);die();
        }
        return $idItem;
    }

}
