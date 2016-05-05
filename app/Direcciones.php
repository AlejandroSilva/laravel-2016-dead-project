<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direcciones extends Model {
    // llave primaria
    public $primaryKey = 'idLocal';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function comuna(){
        // ToDo: falta por revisar
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Comunas', 'cutComuna', 'cutComuna');
    }

    public function local(){
        // ToDo: pendiente
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        //return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
}