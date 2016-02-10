<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model {
    // llave primaria
    public $primaryKey = 'idCliente';
    // no tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function locales(){
        // ToDo: implementar
    }
}
