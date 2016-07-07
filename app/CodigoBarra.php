<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodigoBarra extends Model {
    protected $table = 'codigos_barra';
    public $timestamps = false;
    public $primaryKey = 'barra';
    public $incrementing = false;   // importantisima para cuando el PK sea un varchar
    protected $fillable = ['barra', 'codArticuloAF'];

    // #### Relaciones
    // un ArticulosAF puede tener muchos Barras
    public function articuloAF(){
        return $this->belongsTo('App\ArticuloAF', 'codArticuloAF', 'codArticuloAF');
    }
}
