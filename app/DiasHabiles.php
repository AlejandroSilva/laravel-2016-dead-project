<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DiasHabiles extends Model {
    // IMPORTANTE, NO DEJAR "fecha" COMO PK, SI SE HACE ESTO, ENTONCES
    // AL HACER UN FIND/WHERE, EL CAMPO FECHA ENTREGARA SOLO EL AÑO, NO LA FECHA COMPLETA
    // EDIT 1: al parecer es posible dejarla como PK, pero es necesario indicar que el campo no es incremental:
    // public $incrementing = false;
    // llave primaria
    public $primaryKey = 'fecha';
    // la PK no es numerica
    public $incrementing = false;
    // este modelo no tiene timestamps
    public $timestamps = false;

    // ####  Getters
    function getDiasHabilesTranscurridosMes(){
        $_fecha = explode('-', $this->fecha);
        $anno = $_fecha[0];
        $mes  = $_fecha[1];
        return  $this
            ->whereRaw("extract(year from fecha) = ?", [$anno])
            ->whereRaw("extract(month from fecha) = ?", [$mes])
            ->where('habil', '=', '1')
            // IMPORTANTE: Los dias transcurridos del mes, INCLUYEN al dia actual
            ->where('fecha', '<=', $this->fecha)
            ->count();
    }
    function getDiasHabilesRestantesMes(){
        $_fecha = explode('-', $this->fecha);
        $anno = $_fecha[0];
        $mes  = $_fecha[1];
        return  $this
            ->whereRaw("extract(year from fecha) = ?", [$anno])
            ->whereRaw("extract(month from fecha) = ?", [$mes])
            ->where('habil', '=', '1')
            // IMPORTANTE: Los dias restantes del mes, NO INCLUYEN al dia actual
            ->where('fecha', '>', $this->fecha)
            ->count();
    }

    // #### Helpers
    function diasHabilesAntes($cantidad){
        return DiasHabiles::
            whereRaw('habil = true')
            ->whereRaw("fecha <= '$this->fecha'")
            ->orderBy('fecha', 'desc')
            ->skip($cantidad)
            ->take(1)
            ->first();
    }
    function diasHabilesDespues($cantidad){
        return DiasHabiles::
        whereRaw('habil = true')
            ->whereRaw("fecha >= '$this->fecha'")
            ->orderBy('fecha', 'asc')
            ->skip($cantidad)
            ->take(1)
            ->first();
    }
    static function diaDeLaSemana($fecha){
        // todo: que pasa con las fechas sin el dia fijado? (ej 2016-03-00)
        $numero = Carbon::parse($fecha)->dayOfWeek;
        //$dow = ['do', 'lu', 'ma', 'mi', 'ju', 'vi', 'sá'];
        $dow = ['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SÁ'];
        return $dow[$numero];
    }

    // #### Scopes para hacer Querys/Busquedas
    function scopeGetDiasHabilesMes(){
        $_fecha = explode('-', $this->fecha);
        $anno = $_fecha[0];
        $mes  = $_fecha[1];
        return  $this
            ->whereRaw("extract(year from fecha) = ?", [$anno])
            ->whereRaw("extract(month from fecha) = ?", [$mes])
            ->where('habil', '=', '1')
            ->count();
    }
}
