<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonas extends Model{
    // llave primaria
    public $primaryKey = 'idZona';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function regiones(){
        // hasMany(modelo, child.fogeignKey, this.localKey)
        return $this->hasMany('App\Regiones', 'idZona', 'idZona');
    }
}
