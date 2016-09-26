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
    //Acciones
    static function agregarArchivoMaestra($user, $archivoFinal){
        ArchivoMaestraFCV::create([
            'idUsuarioSubida' => $user? $user->id : null,
            'nombreArchivo' => $archivoFinal['nombre_archivo'],
            'nombreOriginal' => $archivoFinal['nombre_original'],
            'resultado' => 'acta cargada correctamente'
        ]);
    }
}