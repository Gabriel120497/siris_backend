<?php

namespace App\Http\Controllers;

use App\Instrumento;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstrumentoController extends Controller {

    public function instrumentos(){
        $instrumentos=Instrumento::all();

        return response()->json([
            'code' => 200,
            'status'=> 'sucess',
            'instrumentos'=> $instrumentos
        ]);
    }
}
