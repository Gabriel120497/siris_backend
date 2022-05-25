<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo_Usuario extends Model {
    protected $table = "grupos_usuarios";

    public function groups() {
        return $this->belongsTo('App\Grupo', 'id_grupo');
    }

    public function users() {
        return $this->belongsTo('App\Usuario', 'id_usuario');
    }
}
