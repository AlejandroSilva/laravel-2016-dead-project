<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivoRespuestaWOM extends Model {
    public $table = 'archivos_respuesta_wom';
    // llave primaria
    public $primaryKey = 'idArchivoRespuestaWOM';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idSubidoPor', 'idAuditoria', 'nombreArchivo', 'nombreOriginal', 'archivoValido', 'resultado' ];

    // #### Relaciones
    function subidoPor(){
        return $this->hasOne('App\User', 'id', 'idSubidoPor');
    }

    // #### Helpers
    // #### Acciones
    // #### Getters
    static function getPathCarpeta($nombreCliente){
        return public_path()."/$nombreCliente/archivos-respuesta/";
    }
    function getFullPath(){
        $cliente  = Clientes::find(9)->nombreCorto;
        return self::getPathCarpeta($cliente).$this->nombreArchivo;
    }

    // #### Setters
    function setResultado($mensaje, $archivoValido){
        $this->resultado = $mensaje;
        $this->archivoValido = $archivoValido;
        $this->save();
    }

    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
