<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventarios extends Model {
    // llave primaria
    public $primaryKey = 'idInventario';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function locales(){
        // ToDo: por implementar
    }

    public function jornada(){
        // ToDo: por implementar
    }

    public function nominas(){
        // ToDo: por implementar
    }
}
