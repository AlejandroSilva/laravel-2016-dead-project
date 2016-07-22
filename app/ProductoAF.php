<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoAF extends Model {
    protected $table = 'productos_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'SKU';
    public $incrementing = false;   // importantisima para cuando el PK sea un varchar
    protected $fillable = ['SKU', 'descripcion', 'valorMercado'];
    
    // #### Relaciones
    // un ProductoAF, tiene muchos ArticulosAF
    public function articulosAF(){
        return $this->hasMany('App\ArticuloAF', 'SKU', 'SKU');
    }

    // #### Formatear
    static function formato_tablaProductosAF($producto){
        return [
            'SKU' => $producto->SKU,    // entregar como string (por eso no puede ser PK)
            'descripcion' => $producto->descripcion,
            'valorMercado' => $producto->valorMercado,
        ];
    }
}
