<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auditorias extends Model {
    // llave primaria
    public $primaryKey = 'idAuditoria';
    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    public function auditor(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idAuditor');
    }

    // ####  Getters
    //

    // ####  Setters
    //

    // #### Formatear respuestas
    // utilizado por: VistaGeneralController@api_vista
    static function formatear_vistaGeneral($auditoria){
        return (object) [
            'id' => $auditoria->idAuditoria,
            'fechaProgramada' => $auditoria->fechaProgramada,
            'idAuditor' => $auditoria->idAuditor,
            // Local
            'local' => $auditoria->local->numero,
            // Cliente
            'cliente' => $auditoria->local->cliente->nombreCorto,
            // Comuna
            'comuna' => $auditoria->local->direccion->comuna->nombre,
        ];
        // para optimizar, se puede guardar en "cache" la relacion auditoria->local->cliente->nombreCorto
    }

    // #### Scopes para hacer Querys
    public function scopeSoloFechasValidas($query){
        // si se selecciona un rango de dias, este podria llegar a incluir fechas sin el dia fijado, Ej: 2016-06-00
        // este query remove todas las fechas que no tengan el dia fijado
        $query->whereRaw("extract(day from fechaProgramada) != 0");
    }
    public function scopeFechaProgramadaEntre($query, $fechaInicio, $fechaFin){
        // al parecer funciona, hacer mas pruebas
        $query->where('fechaProgramada', '>=', $fechaInicio);
        $query->where('fechaProgramada', '<=', $fechaFin);
    }
}
