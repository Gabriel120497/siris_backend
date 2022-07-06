<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model {
    //use SoftDeletes;
    protected $table = "grupos";
    protected $fillable = ['nombre', 'descripcion', 'cupos_totales', 'cupos_restantes', 'prerequisitos'];
    //protected $dates = [ 'deleted_at' ];

    public function groups_users() {
        return $this->hasMany('App/Grupo_Usuario');
    }
}

