<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormatoLocales extends Model {
    // llave primaria
    public $primaryKey = 'idFormatoLocal';
    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
//    public function locales(){
//        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
//        return $this->belongsTo('App\Provincias', 'cutProvincia', 'cutProvincia');
//    }
}
