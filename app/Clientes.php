<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// Carbon
use Carbon\Carbon;

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

    // #### Acciones
    function agregarArchivoMaestra($user, $archivo){
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $cliente = $this->nombreCorto;
        $extra = "[$timestamp][$cliente]";
        $carpetaArchivosMaestra = ArchivoMaestraProductos::getPathCarpeta($cliente);

        // mover el archivo a la carpeta que corresponde
        $archivoFinal = \ArchivosHelper::moverACarpeta($archivo, $extra, $carpetaArchivosMaestra);

        return ArchivoMaestraProductos::create([
            'idCliente' => $this->idCliente,
            'idSubidoPor' => $user? $user->id : null,
            'nombreArchivo' => $archivoFinal->nombre_archivo,
            'nombreOriginal' => $archivoFinal->nombre_original,
            'resultado' => 'archivo en proceso'
        ]);
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
