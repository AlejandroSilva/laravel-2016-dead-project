<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model {
    // llave primaria
    public $primaryKey = 'idCliente';
    // tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function locales(){
        // ToDo: implementar
    }
}
