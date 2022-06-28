<?php

namespace App\Http\Controllers;

use App\Instrumento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function instrumentosDisponibles() {

        $instrumetosInventario = Instrumento::select('nombre', DB::raw('count(*) as total'))
            ->where('estatus', '=', 'Disponible')
            ->groupBy('nombre')
            ->get();
        $data = array(
            'Disponibles' => $instrumetosInventario
        );
        //var_dump($instrumetosInventario);die();
        return response()->json($data);
    }
}
