<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locales extends Model{
    // llave primaria
    public $primaryKey = 'idLocal';

    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    public function cliente(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Clientes', 'idCliente', 'idCliente');
    }

    public function formatoLocal(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\FormatoLocales', 'idFormatoLocal', 'idFormatoLocal');
    }

    public function direccion(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\Direcciones', 'idLocal', 'idLocal');
    }

    public function inventarios(){
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Inventarios', 'idLocal', 'idLocal');
    }

    public function jornada(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Jornadas', 'idJornadaSugerida', 'idJornada');
    }

    public function llegadaSugerida(){
        // La hora de llegada sugerida corresponde a 1 hora y media antes del cierre de local
        if($this->horaCierre=='00:00:00')
            return '00:00:00';
        else
            return date('H:i:s', strtotime($this->horaCierre)-5400); // 5400 = 90min * 60seg
    }

    public function dotacionSugerida(){
        // El ultimo stock actualizado / Produccion del tipo de local
        $producion = $this->formatoLocal->produccionSugerida;
        $stock = $this->stock;

        return round($stock/$producion);
    }
}
