<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlmacenAF extends Model {
    protected $table = 'almacenes_activo_fijo';
    public $timestamps = false;
    public $primaryKey = 'idAlmacenAF';
    protected $fillable = ['idLocal', 'idUsuarioResponsable', 'nombre'];

    // #### Relaciones
    // muchos almacenes pertenecen a un Local/Ceco
    public function local(){
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    // Almacen tiene muchos ActivosFijos
    public function productosAF(){
        return $this->hasMany('App\ProductoAF', 'idAlmacenAF', 'idAlmacenAF');
    }
    // Almacen tiene muchas preguias de despacho
    // tiene una relacion de idAlmacenOrigen y idAlmacenDestino
//    public function preguias(){
//        return $this->hasMany('App\PreguiaDespacho', 'idAlmacenAF', 'idAlmacenAF');
//    }
}
