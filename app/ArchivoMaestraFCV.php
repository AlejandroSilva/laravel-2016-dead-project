<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivoMaestraFCV extends Model{
    public $table = 'archivo_maestra_fcv';
    // PK
    public $primaryKey = 'idArchivoMaestra';

    public $timestamps = true;
    //Campos asignables
    protected $fillable = ['idUsuarioSubida',  'nombreArchivo', 'nombreOriginal', 'resultado'];
    
    //Relaciones
    function usuario(){
        return $this->belongsTo('App\User', 'idUsuarioSubida','id');
    }
}