<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;

class ArchivoMuestraVencimientoFCV extends Model {
    public $table = 'archivos_muestra_vencimiento_fcv';
    // llave primaria
    public $primaryKey = 'idArchivoMuestraVencimientoFCV';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = ['idSubidoPor', 'nombreArchivo', 'nombreOriginal', 'resultado' ];

    // #### Relaciones
    public function subidoPor(){
        return $this->hasOne('App\User', 'id', 'idSubidoPor');
    }
    function muestras(){
        return $this->hasMany('App\MuestraVencimientoFCV', 'idArchivoMuestraVencimientoFCV', 'idArchivoMuestraVencimientoFCV');
    }

    // #### Helpers
    // #### Acciones

    // #### Getters
    static function getPathCarpetaArchivos(){
        return public_path()."/FCV/muestras-vencimiento/";
    }
    function getFullPath(){
        return self::getPathCarpetaArchivos().$this->nombre_archivo;
    }
    // #### Setters
    function setResultado($mensaje, $muestraValida){
        $this->resultado = $mensaje;
        $this->muestraValida = $muestraValida;
        $this->save();
    }
    function agregarDatos($datos){
        //$this->muestras()->insert($datos);
        DB::transaction(function () use($datos){
            DB::table('muestras_vencimiento_fcv')->insert($datos);
        });
    }

    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
