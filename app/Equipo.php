<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model {
    protected $table = "equipos";
    protected $fillable = ['estatus'];

    public function reservas(){
        return $this->hasMany('App/Reserva');
    }
}
