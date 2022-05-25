<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model {
    protected $table = "equipos";

    public function reservas(){
        return $this->hasMany('App/Reserva');
    }
}
