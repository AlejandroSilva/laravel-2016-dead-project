<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provincias extends Model{
    // llave primaria
    public $primaryKey = 'cutProvincia';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function region(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Regiones', 'cutRegion', 'cutRegion');
    }
    public function comunas(){
        // hasMany(modelo, child.fogeignKey, this.localKey)
        return $this->hasMany('App\Comunas', 'cutProvincia', 'cutProvincia');
    }
}
