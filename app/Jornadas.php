<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jornadas extends Model {
    // llave primaria
    public $primaryKey = 'idJornada';

    // este modelo no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function locales(){
        // los 'locales' tienen una jornada
        // ToDo: por implementar
    }

    public function inventarios(){
        // los 'inventarios' se toman en una 'jornada'
        // ToDo: por implementar
    }

    public function nominas(){
        // las 'nominas' se toman en una 'jornada'
        // ToDo: por implementar
    }
}
