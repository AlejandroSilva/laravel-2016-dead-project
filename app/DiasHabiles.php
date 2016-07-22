<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiasHabiles extends Model {
    // IMPORTANTE, NO DEJAR "fecha" COMO PK, SI SE HACE ESTO, ENTONCES
    // AL HACER UN FIND/WHERE, EL CAMPO FECHA ENTREGARA SOLO EL AÑO, NO LA FECHA COMPLETA
    // llave primaria
    // public $primaryKey = 'fecha';

    // este modelo no tiene timestamps
    public $timestamps = false;

    public function scopeGetDiasHabilesMes(){
        $_fecha = explode('-', $this->fecha);
        $anno = $_fecha[0];
        $mes  = $_fecha[1];
        return  $this
            ->whereRaw("extract(year from fecha) = ?", [$anno])
            ->whereRaw("extract(month from fecha) = ?", [$mes])
            ->where('habil', '=', '1')
            ->count();
    }

    public function getDiasHabilesTranscurridosMes(){
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
    
    public function getDiasHabilesRestantesMes(){
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
}
