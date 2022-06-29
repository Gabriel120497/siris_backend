<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model {
    protected $table = "instrumentos";
    protected $fillable = ['estatus'];

    public function reservas() {
        return $this->hasMany('App/Reserva');
    }
}
