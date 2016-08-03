<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloAF extends Model {
    protected $table = 'acticulos_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idArticuloAF';
    //public $incrementing = false;   // importantisima para cuando el PK sea un varchar
    protected $fillable = ['SKU', 'idArticuloAF', 'fechaIncorporacion', 'stock'];


    // #### Relaciones
    // muchos ArticulosAF perteneces o se originan en el mismo ProductoAF 
    public function productoAF(){
        return $this->belongsTo('App\ProductoAF', 'SKU', 'SKU');
    }

    // cada articulo puede tener muchos codigos de barra, estos deben ser unicos
    public function barras(){
        return $this->hasMany('App\CodigoBarra', 'idArticuloAF', 'idArticuloAF');
    }

    // un articulo puede estar en multiples almacenes
    public function existencias_en_almacenes(){
        return $this->belongsToMany('App\AlmacenAF', 'almacenAF_articuloAF', 'idArticuloAF', 'idAlmacenAF')
            ->withPivot('stockActual');
    }

    // table intermedia artiulos-almacenes
    // Un articulo, puede tener stock distribuido en muchos almacenes
    public function almacenes(){
        return $this->belongsToMany('App\AlmacenAF', 'almacenAF_articuloAF', 'idArticuloAF', 'idAlmacenAF')
            ->withPivot('stockActual');
    }

    // tabla intermedia preguia-articulos
    public function preguias(){
        return $this->belongsToMany('App\PreguiaDespacho', 'preguia_articulo', 'idArticuloAF', 'idPreguia')
            ->withPivot('stockEntregado', 'stockRetornado');
    }
    
    // #### Formatear
    static function formato_tablaArticulosAF($articulo){
        return [
            'idArticuloAF'=> $articulo->idArticuloAF,
            // articulo
            'SKU' => $articulo->SKU,
            'descripcion' => $articulo->productoAF->descripcion,
            'stock' => $articulo->stock,
            'barras' => $articulo->barras->map(function($barra){
                return $barra->barra;
            }),
            //'almacen' => $articulo->almacenAF->nombre,
        ];
    }

    static function formato_conExistenciasPorAlmacen($articulo){
        return [
            'idArticuloAF'=> $articulo->idArticuloAF,
            // articulo
            'SKU' => $articulo->SKU,
            'descripcion' => $articulo->productoAF->descripcion,
            'stock' => $articulo->stock,
            'barras' => $articulo->barras->map(function($barra){
                return $barra->barra;
            }),
            'existencias' => $articulo->existencias_en_almacenes
                ->map(function($almacenArticulo){
                    return [
                        'idAlmacenAF' => $almacenArticulo->pivot->idAlmacenAF,
                        'idArticuloAF' => $almacenArticulo->pivot->idArticuloAF,
                        'stockActual' => $almacenArticulo->pivot->stockActual
                    ];
                })
        ];
    }
}
