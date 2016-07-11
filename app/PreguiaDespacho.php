<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreguiaDespacho extends Model {
    protected $table = 'preguias_despacho';
    public $primaryKey = 'idPreguia';
    public $timestamps = true;
    protected $fillable = [
        'idPreguia', 'idAlmacenOrigen', 'idAlmacenDestino',
        'descripcion', 'fechaEmision', 'montoNeto', 'iva', 'impuestoAdicional', 'total'
    ];

    // #### Relaciones
    public function almacenOrigen(){
        return $this->hasOne('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenOrigen');
    }
    public function almacenDestino(){
        return $this->hasOne('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenDestino');
    }

    // tabla intermedia preguia-articulos
    public function articulos(){
        return $this->belongsToMany('App\ArticuloAF', 'preguia_articulo', 'idPreguia', 'codArticuloAF')
        ->withPivot('estado');
    }

    // #### Formatear
    static function formato_tabla($preguia){
        //return $preguia;
        return [
            'idPreguia' => $preguia->idPreguia,
            'descripcion' => $preguia->descripcion,
            'idAlmacenDestino' => $preguia->idAlmacenDestino,
            'idAlmacenOrigen' => $preguia->idAlmacenOrigen,
            'fechaEmision' => $preguia->fechaEmision,
            'almacenOrigen' => $preguia->almacenOrigen->nombre,
            'almacenDestino' => $preguia->almacenDestino->nombre,
        ];
    }
    static function formato_retornoArticulos($preguia){
        return [
            'idPreguia' => $preguia->idPreguia,
            'descripcion' => $preguia->descripcion,
            'fechaEmision' => $preguia->fechaEmision,
            'descripcion' => $preguia->descripcion,
            'articulos' => $preguia->articulos->map(function($articulo){
                return [
                    'codArticuloAF' => $articulo->codArticuloAF,
                    'SKU' => $articulo->SKU,
                    'idAlmacen' => $articulo->idAlmacenAF,
//                    fechaIncorporacion
                    'descripcion' => $articulo->productoAF->descripcion,
                    'almacen' => $articulo->almacenAF->nombre,
                    'estado' => $articulo->pivot->estado,
                ];
            })
        ];
    }
}

/*
 * Estados:
 * 0 entregado
 * 1 retornado
 * 
 */