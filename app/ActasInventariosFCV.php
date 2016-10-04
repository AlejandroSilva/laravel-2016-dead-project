<?php

namespace App;

use Faker\Provider\cs_CZ\DateTime;
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
    // aud2 = items_auditados
    // aud5 = items_corregidos_auditoria
    // tot1 = fin_captura
    // tot2 = ptt_inventariadas
    // tot3 = total_items_inventariados
    // tot4 = unidades

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
    static function calcularTotales($inventarios) {
        $unidadesInventariadas = 0;
        $minutosTrabajados = 0;
        $nota_total = 0;
        $nota_disponible = 0;
        $porcentajeError_total = 0;
        $porcentajeError_disponible = 0;
        $itemsRevisadosCliente = 0;
        $porcentajeRevision_total = 0;
        $porcentajeRevision_disponible = 0;
        $consolidadoPatentesFCV_total = 0;
        $diferenciaNeta_total = 0;
        foreach ($inventarios as $inv) {
            $acta = $inv->actaFCV;
            $datosDisponibles = $acta && $acta->estaPublicada();

            if($datosDisponibles){
                $unidadesInventariadas += $acta->getUnidadesInventariadas();
                $minutosTrabajados += $acta->getDuracionConteo();
                $itemsRevisadosCliente = $acta->getItemRevisadosCliente();
                $consolidadoPatentesFCV_total += $acta->getConsolidadoPatentes(false);
                $diferenciaNeta_total += $acta->getDiferenciaNeto();
            }
            // promediar solo si los datos estan disponibles y existen dentro del acta
            if($datosDisponibles && $acta->getNotaPromedio()){
                $nota_total += $acta->getNotaPromedio();
                $nota_disponible += 1;
            }
            // promediar solo si los datos estan disponibles y existen dentro del acta
            if($datosDisponibles && $acta->getPorcentajeErrorSei()){
                $porcentajeError_total += $acta->getPorcentajeErrorSei();
                $porcentajeError_disponible += 1;
            }
            // promediar solo si los datos estan disponibles y existen dentro del acta
            if($datosDisponibles && $acta->getPorcentajeRevisionCliente()){
                $porcentajeRevision_total += $acta->getPorcentajeRevisionCliente();
                $porcentajeRevision_disponible += 1;
            }
        };
        $notas_promedio = $nota_disponible>0? number_format($nota_total/$nota_disponible, 1, ',', '.') : '';
        $porcentajeError_promedio = $porcentajeError_disponible>0? number_format($porcentajeError_total/$porcentajeError_disponible, 1, ',', '.').'%' : '';
        $porcentajeRevision_promedio = $porcentajeRevision_disponible>0? number_format($porcentajeRevision_total/$porcentajeRevision_disponible, 1, ',', '.').'%' : '';
        return (object)[
            'unidadesInventariadas' => number_format($unidadesInventariadas, 0, ',', '.'),
            'horasTrabajadas' => gmdate('H:i:s', $minutosTrabajados),
            'nota_promedio' => $notas_promedio,
            'porcentajeError_promedio' => $porcentajeError_promedio,
            'itemsRevisadosCliente' => number_format($itemsRevisadosCliente, 0, ',', '.'),
            'porcentajeRevisionCliente_promedio' => $porcentajeRevision_promedio,
            'consolidadoPatentesFCV_total' => number_format($consolidadoPatentesFCV_total , 0, ',', '.'),
            'diferenciaNeta_total' =>  '$ '.number_format($diferenciaNeta_total, 0, ',', '.')
        ];
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
    private function _getDiferenciaTiempo($inicio, $fin, $conFormato){
        // fechas validas?
        if($inicio==null || $fin==null)
            return null;

        $td_inicio = Carbon::parse($inicio);
        $td_fin = Carbon::parse($fin);

        $diferencia = $td_fin->diffInSeconds($td_inicio);
        // H:i:s = hora min seg
        return $conFormato? gmdate('H:i:s', $diferencia) : $diferencia;
    }
    private function _getDatetime($datetime, $conFormato){
        if($datetime==null || $datetime=='0000-00-00 00:00:00')
            return null;
        return $conFormato? date_format(date_create($datetime), 'H:i:s') : $datetime;
    }
    private function _getEnteroEnMiles($numero, $conFormato){
        if($numero==null)
            return null;
        return $conFormato? number_format($numero, 0, ',', '.') : $numero;
    }
    private function _getFloat($numero, $conFormato){
        if($numero==null)
            return null;
        return $conFormato? number_format($numero, 1, ',', '.') : $numero;
    }

    // hitos importantes del proceso de inventario
    function getInicioConteo($conFormato=false){
        return $this->_getDatetime($this->captura_uno, $conFormato);
    }
    function getFinConteo($conFormato=false){
        return $this->_getDatetime($this->fin_captura, $conFormato);
    }
    function getFinProceso($conFormato=false){
        return $this->_getDatetime($this->fecha_revision_grilla, $conFormato);
    }
    // duración
    function getDuracionConteo($conFormato=false){
        // desde el "inicio del conteo", hasta el "fin del conteo"
        $inicio = $this->getInicioConteo();
        $fin = $this->getFinConteo();
        return $this->_getDiferenciaTiempo($inicio, $fin, $conFormato);
    }
    function getDuracionRevision($conFormato=false){
        // desde el "fin del conteo", hasta el "fin del proceso"
        $inicio = $this->getFinConteo();
        $fin = $this->getFinProceso();
        return $this->_getDiferenciaTiempo($inicio, $fin, $conFormato);
    }
    function getDuracionTotalProceso($conFormato=false){
        // desde el "inicio del conteo", hasta el "fin del proceso"
        $inicio = $this->getInicioConteo();
        $fin = $this->getFinProceso();
        return $this->_getDiferenciaTiempo($inicio, $fin, $conFormato);
    }
    // dotaciones
    function getDotacionPresupuestada($conFormato=false){
        return $this->_getEnteroEnMiles($this->presupuesto, $conFormato);
    }
    function getDotacionEfectiva($conFormato=false){
        return $this->_getEnteroEnMiles($this->efectiva, $conFormato);
    }
    // unidades
    function getUnidadesInventariadas($conFormato=false){
        return $this->_getEnteroEnMiles($this->unidades, $conFormato);
    }
    function getUnidadesTeoricas($conFormato=false){
        return $this->_getEnteroEnMiles($this->teorico_unidades, $conFormato);
    }
    function getDiferenciaNeto($conFormato=false){
        return $this->_getEnteroEnMiles($this->aju2, $conFormato);
    }
    function getDiferenciaAbsoluta($conFormato){
        return $this->_getEnteroEnMiles($this->diferencia_unid_absoluta, $conFormato);
    }
    // evaluaciones / notas
    function getNotaPresentacion(){
        return $this->_getFloat($this->nota1, false);
    }
    function getNotaSupervisor(){
        return $this->_getFloat($this->nota2, false);
    }
    function getNotaConteo(){
        return $this->_getFloat($this->nota3, false);
    }
    function getNotaPromedio($conFormato=false){
        $nota1 = $this->getNotaPresentacion();
        $nota2 = $this->getNotaSupervisor();
        $nota3 = $this->getNotaConteo();
        if($nota1==0 || $nota1==null || $nota2==0 || $nota2==null || $nota3==0 || $nota3==null)
            return null;
        $promedio = ($this->nota1+$this->nota2+$this->nota3)/3;
        return $conFormato? number_format($promedio, 1, ',', '.') : $promedio;
    }
    // Consolidado Auditoria FCV
    function getConsolidadoPatentes($conFormato){
        return $this->_getEnteroEnMiles($this->aud1, $conFormato);
    }
    function getConsolidadoUnidades($conFormato){
        return $this->_getEnteroEnMiles($this->aud3, $conFormato);
    }
    function getConsolidadoItems($conFormato){
        // total_items_inventariados
        return $this->_getEnteroEnMiles($this->aud2, $conFormato);
    }
    // Auditoria QF
    function getAuditoriaQF_patentes($conFormato){
        return $this->_getEnteroEnMiles($this->ptt_rev_qf, $conFormato);
    }
    function getAuditoriaQF_unidades(){
        return '(pendiente)';
    }
    function getAuditoriaQF_items($conFormato){
        return $this->_getEnteroEnMiles($this->items_rev_qf, $conFormato);
    }
    // Auditoria Apoyo 1
    function getAuditoriaApoyo1_patentes($conFormato){
        return $this->_getEnteroEnMiles($this->ptt_rev_apoyo1, $conFormato);
    }
    function getAuditoriaApoyo1_unidades(){
        return '(pendiente)';
    }
    function getAuditoriaApoyo1_items($conFormato){
        return $this->_getEnteroEnMiles($this->items_rev_apoyo1, $conFormato);
    }
    // Auditoria Apoyo 2
    function getAuditoriaApoyo2_patentes($conFormato){
        return $this->_getEnteroEnMiles($this->ptt_rev_apoyo2, $conFormato);
    }
    function getAuditoriaApoyo2_unidades(){
        return '(pendiente)';
    }
    function getAuditoriaApoyo2_items($conFormato){
        return $this->_getEnteroEnMiles($this->items_rev_apoyo2, $conFormato);
    }
    // Auditoria Supervisor
    function getAuditoriaSupervisor_patentes($conFormato){
        return $this->_getEnteroEnMiles($this->ptt_rev_supervisor_fcv, $conFormato);
    }
    function getAuditoriaSupervisor_unidades(){
        return '(pendiente)';
    }
    function getAuditoriaSupervisor_items(){
        return '(pendiente)';
    }
    function getItemRevisadosCliente($conFormato=false){
        // esteban lee el dato: "item_revisado"
        $totalRevisados = $this->items_rev_qf + $this->items_rev_apoyo1 + $this->items_rev_apoyo2;
        return $conFormato? number_format($totalRevisados, 0, ',', '.') : $totalRevisados;
    }

    // Correciones Auditoria FCV a SEI
    function getCorreccionPatentesEnAuditoria($conFormato){
        return $this->_getEnteroEnMiles($this->aud4, $conFormato);
    }
    function getCorreccionItemsEnAuditoria($conFormato){
        return $this->_getEnteroEnMiles($this->aud5, $conFormato);
    }
    function getCorreccionUnidadesNetoEnAuditoria($conFormato){
        return $this->_getEnteroEnMiles($this->aud6, $conFormato);
    }
    function getCorreccionUnidadesAbsolutasEnAuditoria($conFormato){
        return $this->_getEnteroEnMiles($this->unid_absoluto_corregido_auditoria, $conFormato);
    }

    // % Error Aud.
    function getPorcentajeErrorSei($conFormato=false){
        // TODO: si dividen UNIDADES Y ITEMS, PROBLEMA??
        $unidadesCorregidas = $this->getCorreccionUnidadesAbsolutasEnAuditoria(false);
        $itemRevisados = $this->getItemRevisadosCliente();
        // si no estan los datos, no se puede hacer el calculo
        if($itemRevisados==null || $unidadesCorregidas==null)
            return null;
        $porcentaje = ($unidadesCorregidas/$itemRevisados)*100;
        return $conFormato? number_format($porcentaje, 1)."%" : $porcentaje;
    }
    function getPorcentajeErrorQF($conFormato=false){
        return '(pendiente)';
    }

    // Variación Grilla
    function getPorcentajeVariacionGrilla(){
        return '(pendiente)';
    }
    function getSKUInventariados(){
        return '(pendiente)';
    }

    // OTROS
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
