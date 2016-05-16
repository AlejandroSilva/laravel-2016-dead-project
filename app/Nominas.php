<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nominas extends Model {
    // llave primaria
    public $primaryKey = 'idNomina';

    // este modelo tiene timestamps
    public $timestamps = false;

    // #### Relaciones
    public function inventario1() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaDia', 'idNomina');
    }
    public function inventario2(){
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaNoche', 'idNomina');
    }

    public function lider(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idLider');
    }

    public function captador(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idCaptador1');
    }

    public function dotacion(){
        return $this->belongsToMany('App\User', 'nominas_user', 'idNomina', 'idUser')->withTimestamps();
    }
    public function dotacionTitular() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', true)
            ->orderBy('nominas_user.created_at', 'asc');
    }
    public function dotacionReemplazo() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', false)
            ->orderBy('nominas_user.created_at', 'asc');
    }

    // #### Consultas
    public function usuarioEnDotacion($operador){
        return $this->dotacion()->find($operador->id);
    }

    // #### Scopes
//    public function scopeWithLiderCaptadorDotacion($query){
//        return $query->with([
//            'lider',
//            'captador',
//            'dotacion.roles'
//        ]);
//    }
    
    // #### Formatear
    static function formatearSimple($nomina){
        return [
            "idNomina" => $nomina->idNomina,
            "idLider" => $nomina->idLider,
            "idCaptador1" => $nomina->idCaptador1,
            "horaPresentacionLider" => $nomina->horaPresentacionLider,
            "horaPresentacionEquipo" => $nomina->horaPresentacionEquipo,
            "dotacionAsignada" => $nomina->dotacionAsignada,
            "dotacionCaptador1" => $nomina->dotacionCaptador1,
            "fechaSubidaNomina" => $nomina->fechaSubidaNomina
        ];
    }
    static function formatearConLiderCaptadorDotacion($nomina){
        $nominaArray = Nominas::formatearSimple($nomina);
        $nominaArray['lider'] =  User::formatearSimple($nomina->lider);
        $nominaArray['captador']  =  User::formatearSimple($nomina->captador1);
        $nominaArray['dotacionTitular']  =  $nomina->dotacionTitular->map('\App\User::formatearSimple');
        $nominaArray['dotacionReemplazo']  =  $nomina->dotacionReemplazo->map('\App\User::formatearSimple');
        return $nominaArray;
    }
    static function formatearDotacion($nomina){
        return [
            'dotacionTitular' => $nomina->dotacionTitular->map('\App\User::formatearSimple'),
            'dotacionReemplazo' => $nomina->dotacionReemplazo->map('\App\User::formatearSimple')
        ];
    }
}
