<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoMaestra extends Model {
    protected $table = 'productos_maestra';
    public $timestamps = false;
    public $primaryKey = 'idProductoMaestra';
    
    // #### Relaciones
    // PM tiene muchos producto_activo_fijo
    public function productosAF(){
        return $this->hasMany('App\ProductoAF', 'idProductoMaestra', 'idProductoMaestra');
    }
    // PM pertenecen a un cliente
    //public function cliente(){...}
}
