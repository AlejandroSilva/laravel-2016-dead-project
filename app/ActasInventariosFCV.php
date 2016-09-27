<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActasInventariosFCV extends Model {
    public $table = 'actas_inventarios_fcv';
    // llave primaria
    public $primaryKey = 'idActaFCV';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idInventario',
        'ceco_local', 'fecha_inventario', 'cliente', 'rut', 'supervisor', 'quimico_farmaceutico', 'nota_presentacion',
        'nota_supervisor', 'nota_conteo', 'inicio_conteo', 'fin_conteo', 'fin_revisión', 'horas_trabajadas',
        'dotacion_presupuestada', 'dotacion_efectivo', 'unidades_inventariadas', 'unidades_teoricas', 'unidades_ajustadas',
        'ptt_total_inventariadas', 'ptt_revisadas_totales', 'ptt_revisadas_qf', 'ptt_revisadas_apoyo_cv_1',
        'ptt_revisadas_apoyo_cv_2', 'ptt_revisadas_supervisores_fcv', 'item_total_inventariados', 'item_revisados',
        'item_revisados_qf', 'item_revisados_apoyo_cv_1', 'item_revisados_apoyo_cv_2', 'unidades_corregidas_revision_previo_ajuste',
        'unidades_corregidas', 'total_item',
    ];

    // #### Relaciones
    function inventario() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Inventarios', 'idInventario', 'idInventario');
    }

    // #### Helpers

    // #### Acciones
    // #### Getters
    // ####  Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
}
