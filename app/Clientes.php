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

    /**
     * Entrega un listado con todos los clientes, y una vista simplificada de los locales que tiene asociado
     */
    public static function allWithSimpleLocales(){
        // query para obtener la lista de clientes con sus respectivos locales
        $clientes = Clientes::with('locales')->get();

        // a esta lista convertida en array, se mapea para filtrar los campos de los locales (demasiada informacion = consulta lenta)
        return $clientes->map(function($cliente){
            $clienteArray = $cliente->toArray();
            $clienteArray['locales'] = $cliente->locales->map(function($local){
                return [
                    'idLocal'=>$local['idLocal'],
                    'numero'=>$local['numero'],
                    'nombre'=>$local['nombre']
                ];
            });
            return $clienteArray;
        });
    }

    // #### Formatear
    static function formatearSimple($cliente){
        // la tabla es tan simple que no necesita ser modificado
        return $cliente;
    }
}
