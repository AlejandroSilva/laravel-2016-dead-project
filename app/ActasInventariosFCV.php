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
    public function publicadaPor(){
        return $this->hasOne('App\User', 'id', 'idPublicadoPor');
    }

    // #### Helpers
    function estaPublicada(){
        return $this->fecha_publicacion!=null && $this->fecha_publicacion!='0000-00-00 00:00:00';
    }
    // #### Acciones
    function publicar($user){
        $this->fecha_publicacion = Carbon::now();
        $this->idPublicadoPor = $user->id;
        $this->save();
    }
    function despublicar(){
        $this->fecha_publicacion = null;
        $this->idPublicadoPor = null;
        $this->save();
    }

    // #### Getters
    function getInicioConteo(){
        $inicio = $this->captura_uno;
        if($inicio==null || $inicio=='0000-00-00 00:00:00')
            return null;
        return $inicio;
    }
    function getFinConteo(){
        $fin = $this->fin_captura;
        if($fin==null || $fin=='0000-00-00 00:00:00')
            return null;
        return $fin;
    }
    function getHorasTrabajadas(){
        $inicio = $this->getInicioConteo();
        $fin = $this->getFinConteo();
        // fechas validas?
        if($inicio==null || $fin==null)
            return null;

        $td_inicio = Carbon::parse($inicio);
        $td_fin = Carbon::parse($fin);

        $diferencia = $td_fin->diffInSeconds($td_inicio);
        return gmdate('H:i:s', $diferencia);
    }
    function getUnidadesInventariadas($conFormato){
        // dar formato al dato solo si existe
        if($this->unidades==null)
            return null;
        return $conFormato? number_format($this->unidades, 0, ',', '.') : $this->unidades;
    }
    function getUnidadesTeoricas(){
        // dar formato al dato solo si existe
        if($this->teorico_unidades==null)
            return null;
        else
            return number_format($this->teorico_unidades, 0, ',', '.');
    }
    function getItemsHH($conFormato=false){
        // si no hay hora de inicio ni de fin, no se puede calcular cuantas horas se trabajaron
        $inicio = $this->captura_uno;

        if($inicio=='0000-00-00 00:00:00' || $this->fin_captura=='0000-00-00 00:00:00')
            return null;

        // calcular la diferencia en horas, con punto flotante
        $inicio = Carbon::parse($this->captura_uno);
        $fin = Carbon::parse($this->fin_captura);
        $horas_comoFloat = $fin->diffInMinutes($inicio)/60;

        $unidades = $this->unidades;
        $dotacion = $this->efectiva;

        // no dividir por cero
        if($dotacion==0 || $horas_comoFloat==0)
            return null;
        $itemsHH = round($unidades/$dotacion/$horas_comoFloat);
        return $conFormato? number_format($itemsHH, 0, ',', '.') : $itemsHH;
    }
    function getNotaPromedio(){
        if($this->nota1==0 || $this->nota2==0 || $this->nota3==0)
            return null;
        return number_format(($this->nota1+$this->nota2+$this->nota3)/3, 1, ',', '.');
    }
    function getItemCorregido(){
        return $this->unid_absoluto_corregido_auditoria;
    }
    function getItemRevisadosCliente($conFormato=false){
        // esteban lee el dato: "item_revisado"
        $totalRevisados = $this->items_rev_qf + $this->items_rev_apoyo1 + $this->items_rev_apoyo2;
        return $conFormato? number_format($totalRevisados, 0, ',', '.') : $totalRevisados;
    }
    function getPorcentajeErrorSei(){
        $corregidos = $this->getItemCorregido();
        $revisados = $this->getItemRevisadosCliente();
        // si no estan los datos, no se puede hacer el calculo
        if($revisados==null || $corregidos==null)
            return null;
        return number_format(($corregidos/$revisados)*100, 1)."%";
    }
    function getItemTotalContados(){
        return $this->total_items_inventariados;
    }
    function getPorcentajeRevisionCliente($conFormato=false){
        $totalContados = $this->getItemTotalContados();
        $revisados = $this->getItemRevisadosCliente();
        // si no estan los datos, no se puede hacer el calculo
        if($revisados==null || $totalContados==null)
            return null;
        $porcentaje = $revisados/$totalContados * 100;
        return $conFormato? number_format($porcentaje, 1)."%" : $porcentaje;
    }
    function getPatentesRevisadasTotales($conFormato){
        if($this->aud1==null)
            return null;
        return $conFormato? number_format($this->aud1, 0, ',', '.') : $this->aud1;
    }
    function getDiferenciaNeta($conFormato){
        if($this->aju2==null)
            return null;
        return $conFormato? number_format($this->aju2, 0, ',', '.') : $this->aju2;
    }

    // ####  Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    // #### Buscar / Filtrar Nominas
    static function buscar(){
        $query =  ActasInventariosFCV::where('fecha_publicacion', '!=', '0000-00-00 00:00:00');

        // pendinte: ordenar por la fecha programada del inventario, no de la tabla acta
        // $query->orderBy('inventario.fechaProgramada', 'asc');
        return $query->get();
    }
}
