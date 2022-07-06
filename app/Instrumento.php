<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrumento extends Model {
    protected $table = "instrumentos";
    protected $fillable = ['nombre', 'estado', 'descripcion_estado', 'estatus', 'habilitado_para'];

    public function reservas() {
        return $this->hasMany('App/Reserva');
    }
}
