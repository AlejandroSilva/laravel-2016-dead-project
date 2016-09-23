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

    // #### relación entre User y ArchivoFinalInventario para obtener nombre1, apellidoPaterno
    function usuario_auditor(){
        return $this->belongsTo('App\User', 'idSubidoPor','id');
    }
    // #### Helpers
    // #### Acciones
    // #### Getters
    // #### Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
