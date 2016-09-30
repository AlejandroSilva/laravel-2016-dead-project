<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActasInventariosFCV extends Model {
    public $table = 'actas_inventarios_fcv';
    // llave primaria
    public $primaryKey = 'idActaFCV';
    // este modelo tiene timestamps
    public $timestamps = true;
    // campos asignables
    protected $fillable = [ 'idInventario', 'idArchivoFinalInventario',
        'presupuesto', 'efectiva', 'hora_llegada', 'administrador', 'porcentaje', 'captura_uno', 'emision_cero',
        'emision_variance', 'inicio_sumary', 'fin_captura', 'unidades', 'teorico_unidades', 'fecha_toma', 'cod_local',
        'nombre_empresa', 'usuario', 'nota1', 'nota2', 'nota3', 'aud1', 'aud2', 'aud3', 'aud4', 'aud5', 'aud6',
        'aju1', 'aju2', 'aju3', 'aju4', 'tot1', 'tot2', 'tot3', 'tot4', 'check1', 'check2', 'check3', 'check4',
        // version nueva:
        'fecha_revision_grilla', 'supervisor_qf', 'diferencia_unid_absoluta', 'ptt_inventariadas', 'ptt_rev_qf',
        'ptt_rev_apoyo1', 'ptt_rev_apoyo2', 'ptt_rev_supervisor_fcv', 'total_items_inventariados', 'items_auditados',
        'items_corregidos_auditoria', 'items_rev_qf', 'items_rev_apoyo1', 'items_rev_apoyo2', 'unid_neto_corregido_auditoria',
        'unid_absoluto_corregido_auditoria',
    ];

    // #### Relaciones
    function inventario() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Inventarios', 'idInventario', 'idInventario');
    }

    // #### Helpers
    // #### Acciones
    // #### Getters
    function getHorasTrabajadas(){
        // fechas validas?
        if($this->captura_uno=='0000-00-00 00:00:00' || $this->fin_captura=='0000-00-00 00:00:00')
            return null;

        $inicio = Carbon::parse($this->captura_uno);
        $fin = Carbon::parse($this->fin_captura);

        $total = $fin->diffInSeconds($inicio);
        return gmdate('H:i:s', $total);
    }

    // ####  Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
    static function buscar(){
        return ActasInventariosFCV::get();
    }
}
