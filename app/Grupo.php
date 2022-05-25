<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model {
    protected $table = "grupos";

    public function groups_users() {
        return $this->hasMany('App/Grupo_Usuario');
    }
}

