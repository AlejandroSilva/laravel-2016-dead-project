<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivoFinalInventario extends Model {
    public $table = 'archivos_finales_inventarios';
    // llave primaria
    public $primaryKey = 'idArchivoFinalInventario';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idInventario', 'idSubidoPor', 'nombre_archivo', 'nombre_original', 'resultado' ];

    // #### Relaciones
    function inventario() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Inventarios', 'idInventario', 'idInventario');
    }
    public function subidoPor(){
        return $this->hasOne('App\User', 'id', 'idSubidoPor');
    }

    // #### relación entre User y ArchivoFinalInventario para obtener nombre1, apellidoPaterno
    function usuario_auditor(){
        return $this->belongsTo('App\User', 'idSubidoPor','id');
    }
    // #### Helpers
    // #### Acciones
    // #### Getters
    static function getPathCarpetaArchivos($cliente){
        return public_path()."/$cliente/archivoFinalInventario/";
    }
    function getFullPath(){
        $cliente = $this->inventario->local->cliente;
        return self::getPathCarpetaArchivos($cliente->nombreCorto).$this->nombre_archivo;
    }

    // #### Setters
    function setResultado($mensaje, $actaValida){
        $this->resultado = $mensaje;
        $this->actaValida = $actaValida;
        $this->save();
    }

    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
