<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaestraFCV extends Model{
    public $table = 'maestra_fcv';
    // PK
    public $primaryKey = 'idMaestraFCV';
    public $timestamps = true;
    // Campos asignables
    protected $fillable = ['idArchivoMaestra',  'codigoProducto', 'descriptor', 'codigo', 'laboratorio', 'clasificacionTerapeutica'];
    //Relaciones
    public function archivoMaestraFCV(){
        return $this->belongsTo('App\ArchivoMaestraFCV', 'idArchivoMaestra', 'idArchivoMaestra');
    }
}