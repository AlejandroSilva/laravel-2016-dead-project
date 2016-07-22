<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subgeo extends Model {
    // llave primaria
    public $primaryKey = 'idSubgeo';

    // #### Relaciones
    public function geo(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Geo', 'idGeo', 'idGeo');
    }
}
