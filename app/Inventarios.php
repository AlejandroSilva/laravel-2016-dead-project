<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventarios extends Model {
    // llave primaria
    public $primaryKey = 'idInventario';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }

//    public function jornada(){
//        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
//        return $this->hasOne('App\Jornadas', 'idJornada', 'idJornada');
//    }

    public function nominas(){
        // ToDo: por implementar
    }
}
