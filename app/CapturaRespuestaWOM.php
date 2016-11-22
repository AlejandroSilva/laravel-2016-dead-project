<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapturaRespuestaWOM extends Model {
    public $table = 'capturas_respuesta_wom';
    // llave primaria
    public $primaryKey = 'idCapturaAuditoriaWOM';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idArchivoRespuestaWOM',
        'line', 'ptt', 'correlativo', 'sku', 'serie', 'conteoInicial', 'conteoFinal',
        'estado', 'codigoOrganizacion', 'nombreOrganizacion', 'fechaCaptura', 'horaCaptura'];

    // #### Relaciones
//    function subidoPor(){
//        return $this->hasOne('App\User', 'id', 'idSubidoPor');
//    }

    // #### Helpers
    // #### Acciones
    // #### Getters

    // #### Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
    static function buscar($peticion){
        $query = CapturaRespuestaWOM::with([]);

        // Buscar por archivo (opcional)
        if (isset($peticion->idArchivo)) {
            $query->where('idArchivoRespuestaWOM', $peticion->idArchivo);
        }

        // Buscar por patente (opcional)
        if (isset($peticion->patente)) {
            $query->where('ptt', $peticion->patente);
        }

        // Buscar por estado (opcional)
        if (isset($peticion->estado)) {
            $query->where('estado', $peticion->estado);
        }

        return $query->get();
    }
}
