<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comunas extends Model {
    // llave primaria
    public $primaryKey = 'cutComuna';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function provincia(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Provincias', 'cutProvincia', 'cutProvincia');
    }

    public function direcciones(){
        // ToDo: falta por revisar
        // hasMany(modelo, child.fogeignKey, this.localKey)
        return $this->hasMany('App\Direcciones', 'cutComuna', 'cutComuna');
    }
}
