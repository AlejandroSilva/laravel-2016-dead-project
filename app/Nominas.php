<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nominas extends Model {
    // llave primaria
    public $primaryKey = 'idNomina';

    // este modelo tiene timestamps
    public $timestamps = false;

    // #### Relaciones
        public function inventario1() {
            return $this->hasOne('App\Inventarios', 'idNominaDia', 'idNomina');
        }
        public function inventario2(){
            //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
//            return $this->belongsTo('App\Inventarios'/*, 'idLocal', 'idLocal'*/);

//            return $this->hasOne('App\Inventarios', 'idNominaDia', 'idNomina');
//            dd($this->hasOne('App\Inventarios', 'idNominaNoche', 'idNomina'));
            return $this->hasOne('App\Inventarios', 'idNominaNoche', 'idNomina');
        }
}
