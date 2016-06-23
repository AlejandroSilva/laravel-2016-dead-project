<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoAF extends Model {
    protected $table = 'productos_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idProductoAF';
    
    
    // #### Relaciones
    // muchos productosAF tienen un mismo productoMaestro
    public function productoMaestra(){
        return $this->belongsTo('App\ProductoMaestra', 'idProductoMaestra', 'idProductoMaestra');
    }
    // muchos productosAF estan almacenados en un AlmacenActivoFijo
    public function almacenAF(){
        return $this->belongsTo('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenAF');
    }
}
