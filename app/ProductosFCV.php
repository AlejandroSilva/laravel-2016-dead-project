<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// DB
use DB;


class ProductosFCV extends Model{
    public $table = 'productos_fcv';
    // PK
    public $primaryKey = 'idProductoFCV';
    public $timestamps = true;
    // Campos asignables
    protected $fillable = ['idArchivoMaestra',  'barra', 'descriptor', 'sku', 'laboratorio', 'clasificacionTerapeutica'];

    // #### Relaciones
    function archivoMaestraFCV(){
        return $this->belongsTo('App\ArchivoMaestraFCV', 'idArchivoMaestra', 'idArchivoMaestra');
    }

    // #### Helpers
}