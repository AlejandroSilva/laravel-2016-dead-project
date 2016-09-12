<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoNominas extends Model {
    // llave primaria
    public $primaryKey = 'idEstadoNomina';
    // este modelo no tiene timestamps
    public $timestamps = false;
    
    #### Relaciones
    //

    // #### Formatear respuestas
    static function formatearSimple($estado){
        return [
            'idEstadoNomina' => $estado->idEstadoNomina,
            'nombre' => $estado->nombre,
            'descripcion' => $estado->descripcion
        ];
    }
}
