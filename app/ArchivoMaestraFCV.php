<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
// DB
use DB;

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
    function maestras(){
        return $this->hasMany('App\MaestraFCV','idArchivoMaestra','idArchivoMaestra');
    }
    static function getPathCarpetaArchivos(){
                return public_path()."/FCV/maestrasFCV/";
     }
    function setResultado($resultado){
        $this->resultado = $resultado;
        $this->save();
        
    }
    function getFullPath(){
           return self::getPathCarpetaArchivos().$this->nombreArchivo;
    }
    static function agregarArchivoMaestra($user, $archivoFinal){
        return ArchivoMaestraFCV::create([
            'idUsuarioSubida' => $user? $user->id : null,
            'nombreArchivo' => $archivoFinal['nombre_archivo'],
            'nombreOriginal' => $archivoFinal['nombre_original'],
            'resultado' => 'acta cargada correctamente'
        ]);
    }
    
    function guardarRegistro($tableData){
        $chunk = array_chunk($tableData,100,true);
        //Alejandro
        /*DB::transaction(function () use($chunk) {
            DB::table('maestra_fcv')->insert($chunk);
        });*/
        // se demora 1,5 min
        DB::transaction(function() use ($chunk) {
            foreach ($chunk as $dato) {
                DB::table('maestra_fcv')->insert($dato);
            }
        });
    }
}