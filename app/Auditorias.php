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
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idAuditor');
    }
    
}
