<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MuestraVencimientoFCV extends Model{
    public $table = 'muestras_vencimiento_fcv';
    // llave primaria
    public $primaryKey = 'idMuestraVencimientoFCV';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = ['idArchivoMuestraVencimientoFCV', 'codigo_producto',
        'descriptor', 'barra', 'laboratorio', 'clasificacion_terapeutica' ];
    // campos que se omite
    protected $guarded = ['row'];


    // #### Relaciones
    public function archivoMuestra(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\ArchivoMuestraVencimientoFCV', 'idArchivoMuestraVencimientoFCV', 'idArchivoMuestraVencimientoFCV');
    }

    // #### Helpers
    // #### Acciones
    // #### Getters
    // #### Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
