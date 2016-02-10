<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regiones extends Model {
    // llave primaria
    public $primaryKey = 'cutRegion';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function zona(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Zonas', 'idZona', 'idZona');
    }

    public function provincias(){
        // hasMany(modelo, child.fogeignKey, this.localKey)
        return $this->hasMany('App\Provincias', 'cutRegion', 'cutRegion');
    }
}
