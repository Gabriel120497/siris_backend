<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model {
    protected $table = "instrumentos";

    public function reservas() {
        return $this->hasMany('App/Reserva');
    }
}
