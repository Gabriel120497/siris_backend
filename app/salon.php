<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model {
    protected $table = "salones";

    public function reservas() {
        return $this->hasMany('App/Reserva');
    }
}
