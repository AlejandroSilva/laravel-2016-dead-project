<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenAF extends Model {
    protected $table = 'almacenes_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idAlmacenAF';
    
    // #### Relaciones
    // Almacen tiene muchos ProductosAF
    public function productosAF(){
        return $this->hasMany('App\ProductoAF', 'idAlmacenAF', 'idAlmacenAF');
    }
    // muchos almacenes pertenecen a un Local/Ceco
    //public function local(){...}
}
