<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenAF_ArticuloAF extends Model {
    protected $table = 'almacenAF_articuloAF';
    public $timestamps = false;
    // laravel no permite tener composite key como PK, solo para que el codigo funcione no se especifica el PK en este modelo
    // public $primaryKey = ['idAlmacenAF', 'idArticuloAF'];  <--- falla
    // protected $fillable = ['idAlmacenAF', 'idArticuloAF', 'stockActual'];

    // un AlmacenArticulo tiene un Almance asociado
    public function almacenAF(){
        return $this->hasOne('App\AlmacenAF', 'idAlmacenAF', 'idAlmacenAF');
    }

    // un AlmacenArticulo tiene un Almance asociado
    public function articuloAF(){
        return $this->hasOne('App\ArticuloAF', 'idArticuloAF', 'idArticuloAF');
    }

    static function formato_tablaArticulos($almaArti) {
        return [
            // articulo
            'sku' => $almaArti->articuloAF->SKU,
            'descripcion' => $almaArti->articuloAF->productoAF->descripcion,
            'barras' => $almaArti->articuloAF->barras->map(function($barra){
                return $barra->barra;
            }),

            // almacen
            'idAlmacenAF' => $almaArti->idAlmacenAF,
            'almacen' => $almaArti->almacenAF->nombre,

            'stockActual'=> $almaArti->stockActual
        ];
    }
}
