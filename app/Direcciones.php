<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direcciones extends Model {
    // llave primaria
    public $primaryKey = 'idLocal';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = ['idLocal', 'cutComuna', 'direccion'];

    // #### Relaciones
    public function comuna(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Comunas', 'cutComuna', 'cutComuna');
    }
//    public function local(){
//        // ToDo: pendiente
//        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
//        //return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
//    }
}