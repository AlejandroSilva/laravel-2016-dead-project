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
        // hasMany(modelo, child.fogeignKey, this.localKey)
        return $this->hasMany('App\Locales', 'idCliente', 'idCliente');
    }

    // #### Formatear respuestas
    static function formatearSimple($cliente){
        // la tabla es tan simple que no necesita ser modificado
        return $cliente;
    }

    // #### Scopes para hacer Querys/Busquedas
    // Entrega un listado con todos los clientes, y una vista simplificada de los locales que tiene asociado
    public static function todos_conLocales(){
        return Clientes::all()->map(function($cliente){
            $cliente->locales = $cliente->locales->map(function($local){
                return [
                    'idLocal'=> $local->idLocal,
                    'numero' => $local->numero,
                    'nombre' => $local->nombre
                ];
            });
            return $cliente;
        });
    }
}
