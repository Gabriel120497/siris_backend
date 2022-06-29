<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model {
    protected $table = "grupos";
    protected $fillable = ['cupos_restantes'];

    public function groups_users() {
        return $this->hasMany('App/Grupo_Usuario');
    }
}

