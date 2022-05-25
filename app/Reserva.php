<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model {
    protected $table = 'reservas';

    public function users() {
        return $this->belongsTo('App/Usuario', 'id_usuario');
    }

    public function instruments() {
        return $this->belongsTo('App\Instrumento', 'id_instrumento');
    }

    public function equipments() {
        return $this->belongsTo('App\Equipo', 'id_equipo');
    }

    public function classrooms() {
        return $this->belongsTo('App\Salon', 'id_salon');
    }
}
