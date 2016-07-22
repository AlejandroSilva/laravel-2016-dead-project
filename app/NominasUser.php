<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NominasUser extends Model {
    // este modelo tiene timestamps
    public $timestamps = true;

//    public function role(){
//        return $this->hasOne('App\Role', 'id', 'idRoleAsignado');
//    }
}
