<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoAF extends Model {
    protected $table = 'productos_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idProductoAF';
    protected $fillable = [
        'idProductoAF', 'codActivoFijo', 'idAlmacenAF',
        'descripcion', 'precio', 'barra1', 'barra2', 'barra3'
    ];
    
    // #### Relaciones
    // muchos ActivosFijos estan almacenados en un AlmacenAF
    public function almacenAF(){
        return $this->belongsTo('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenAF');
    }
    // muchos ActivosFijos estan pertenecen a un local
    public function local(){
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }

    // #### Formatear
    static function formato_tablaProductosAF($producto){
        return [
            'id' => $producto->idProductoAF,
            'codigo' => $producto->codActivoFijo,    // entregar como string (por eso no puede ser PK)
            'descripcion' => $producto->descripcion,
            'precio' => $producto->precio,
            'barra1' => $producto->barra1,
            'barra2' => $producto->barra2,
            'barra3' => $producto->barra3,
            'idAlmacen' => $producto->almacenAF->idAlmacenAF,
            'almacen' => $producto->almacenAF->nombre,
        ];
    }
}
