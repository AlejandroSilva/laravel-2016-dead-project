<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Geo extends Model {
    // llave primaria
    public $primaryKey = 'idGeo';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function zona(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Zonas', 'idZona', 'idZona');
    }
}
