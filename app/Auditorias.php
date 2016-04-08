<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auditorias extends Model {
    // llave primaria
    public $primaryKey = 'idAuditoria';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }

    public function auditor(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
//        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    
}
