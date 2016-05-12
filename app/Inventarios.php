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

    public function nominaDia(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaDia');
        return $this->belongsTo('App\Nominas', 'idNominaDia', 'idNomina');
    }
    public function nominaNoche(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaNoche');
        return $this->belongsTo('App\Nominas', 'idNominaNoche', 'idNomina');
    }

    // #### With scopes
    public function scopeWithTodo($query){
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ]);
    }
}
