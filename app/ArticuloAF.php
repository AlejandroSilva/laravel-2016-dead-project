<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloAF extends Model {
    protected $table = 'acticulos_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'codArticuloAF';
    public $incrementing = false;   // importantisima para cuando el PK sea un varchar
    protected $fillable = ['SKU', 'descripcion', 'valorMercado'];

    // #### Relaciones
    // muchos ArticulosAF perteneces o se originan en el mismo ProductoAF 
    public function productoAF(){
        return $this->belongsTo('App\ProductoAF', 'SKU', 'SKU');
    }

    public function almacenAF(){
        return $this->belongsTo('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenAF');
    }

    // cada articulo puede tener muchos codigos de barra, estos deben ser unicos
    public function barras(){
        return $this->hasMany('App\CodigoBarra', 'codArticuloAF', 'codArticuloAF');
    }

    // tabla intermedia preguia-articulos
    public function preguias(){
        return $this->belongsToMany('App\PreguiaDespacho', 'preguia_articulo', 'codArticuloAF', 'idPreguia')
        ->withPivot('retornado');
    }
    
    // #### Formatear
    static function formato_tablaArticulosAF($articulo){
        return [
            'SKU' => $articulo->SKU,
            'descripcion' => $articulo->productoAF->descripcion,
            'codArt' => $articulo->codArticuloAF,    // entregar como string (por eso no puede ser PK)
            'idAlmacen' => $articulo->idAlmacenAF,
            'almacen' => $articulo->almacenAF->nombre,
            'barras' => $articulo->barras->map(function($barra){
                return $barra->barra;
            }),
        ];
    }
}
