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
        'unid_absoluto_corregido_auditoria', 'total_sku_efectivos', 'porcentaje_error_qf', 'porcentaje_variacion_ajuste_grilla',
        'supervisor_total_unidades', 'supervisor_total_items', 'supervisor_total_patentes',
        'apoyo2_total_unidades', 'apoyo2_total_items', 'apoyo2_total_patentes',
        'apoyo1_total_unidades', 'apoyo1_total_items', 'apoyo1_total_patentes',
        'qf_total_unidades', 'qf_total_items', 'qf_total_patentes',
        'sku_unicos_inventariados'
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
    public function archivoFinal(){
        return $this->hasOne('App\ArchivoFinalInventario', 'idArchivoFinalInventario', 'idArchivoFinalInventario');
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
        if(!isset($numero))
            return null;
        return $conFormato? number_format($numero, 0, ',', '.') : $numero;
    }
    private function _getFloat($numero, $conFormato){
        if($numero==null)
            return null;
        return $conFormato? number_format($numero, 1, ',', '.') : $numero;
    }
    private function _getPorcentaje($porcentaje, $conFormato){
        if($porcentaje==null)
            return null;
        return $conFormato? number_format($porcentaje, 1)."%" : $porcentaje;
    }

    // hitos importantes del proceso de inventario
    function getFechaTomaInventario(){
        return $this->fecha_toma;
    }
    function setFechaTomaInventario($fecha){
        $this->fecha_toma = $fecha;
        $this->save();
    }
    function getCliente(){
        return $this->nombre_empresa;
    }
    function setCliente($cliente){
        $this->nombre_empresa = $cliente;
        $this->save();
    }
    function getCeco(){
        return $this->cod_local;
    }
    function setCeco($ceco){
        $this->cod_local = $ceco;
        $this->save();
    }
    function getSupervisor(){
        return $this->usuario;
    }
    function setSupervisor($supervisor){
        $this->usuario = $supervisor;
        $this->save();
    }
    function getQF(){
        return $this->administrador;
    }
    function setQF($qf){
        $this->administrador = $qf;
        $this->save();
    }
    function getInicioConteo($conFormato=false){
        return $this->_getDatetime($this->captura_uno, $conFormato);
    }
    function setInicioConteo($inicio){
        $this->captura_uno = $inicio;
        $this->save();
    }
    function getFinConteo($conFormato=false){
        return $this->_getDatetime($this->fin_captura, $conFormato);
    }
    function setFinConteo($fincaptura){
        $this->fin_captura = $fincaptura;
        $this->save();
    }
    function getFinProceso($conFormato=false){
        return $this->_getDatetime($this->fecha_revision_grilla, $conFormato);
    }
    function setFinProceso($finProceso){
        $this->fecha_revision_grilla = $finProceso;
        $this->save();
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
    function setDotacionPresupuestada($dotacion){
        $this->presupuesto = $dotacion;
        $this->save();
    }
    function getDotacionEfectiva($conFormato=false){
        return $this->_getEnteroEnMiles($this->efectiva, $conFormato);
    }
    function setDotacionEfectiva($dotacion){
        $this->efectiva = $dotacion;
        $this->save();
    }
    // unidades
    function getUnidadesInventariadas($conFormato=false){
        return $this->_getEnteroEnMiles($this->unidades, $conFormato);
    }
    function setUnidadesInventariadas($unidades){
        $this->unidades = $unidades;
        $this->save();
    }
    function getUnidadesTeoricas($conFormato=false){
        return $this->_getEnteroEnMiles($this->teorico_unidades, $conFormato);
    }
    function setUnidadesTeoricas($unidades) {
        $this->teorico_unidades = $unidades;
        $this->save();
    }
    function getDiferenciaNeto($conFormato=false){
        $real = $this->getUnidadesInventariadas(false);
        $teoricas = $this->getUnidadesTeoricas(false);
        if($real==null || $teoricas==null)
            return null;
        else
            return $this->_getEnteroEnMiles($real-$teoricas, $conFormato);
    }
    function getDiferenciaAbsoluta($conFormato=false){
        return $this->_getEnteroEnMiles($this->diferencia_unid_absoluta, $conFormato);
    }
    function setDiferenciaAbsoluta($unidades){
        $this->diferencia_unid_absoluta = $unidades;
        $this->save();
    }
    // evaluaciones / notas
    function getNotaPresentacion(){
        return $this->_getFloat($this->nota1, false);
    }
    function setNotaPresentacion($nota){
        $this->nota1 = $nota;
        $this->save();
    }
    function getNotaSupervisor(){
        return $this->_getFloat($this->nota2, false);
    }
    function setNotaSupervisor($nota){
        $this->nota2 = $nota;
        $this->save();
    }
    function getNotaConteo(){
        return $this->_getFloat($this->nota3, false);
    }
    function setNotaConteo($nota){
        $this->nota3 =$nota;
        $this->save();
    }
    function getNotaPromedio(){
        $nota1 = $this->getNotaPresentacion();
        $nota2 = $this->getNotaSupervisor();
        $nota3 = $this->getNotaConteo();
        if($nota1==0 || $nota1==null || $nota2==0 || $nota2==null || $nota3==0 || $nota3==null)
            return null;
        $promedio = ($this->nota1+$this->nota2+$this->nota3)/3;
        return number_format($promedio, 1, ',', '.');
    }
    // Consolidado Auditoria FCV
    function getConsolidadoPatentes($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud1, $conFormato);
    }
    function setConsolidadoPatentes($ptt){
        $this->aud1 = $ptt;
        $this->save();
    }
    function getConsolidadoUnidades($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud3, $conFormato);
    }
    function setConsolidadoUnidades($unidades){
        $this->aud3 = $unidades;
        $this->save();
    }
    function getConsolidadoItems($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud2, $conFormato);
    }
    function setConsolidadoItems($items){
        $this->aud2 = $items;
        $this->save();
    }
    // Auditoria QF
    function getAuditoriaQF_patentes($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->qf_total_patentes))
            return $this->_getEnteroEnMiles($this->qf_total_patentes, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->ptt_rev_qf, $conFormato);
    }
    function setAuditoriaQF_patentes($ptt){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->ptt_rev_qf = $ptt;
        $this->qf_total_patentes = $ptt;
        $this->save();
    }
    function getAuditoriaQF_unidades($conFormato=false){
        // solo en la version nueva
        return $this->_getEnteroEnMiles($this->qf_total_unidades, $conFormato);
    }
    function setAuditoriaQF_unidades($unidades){
        // solo en la version nueva
        $this->qf_total_unidades = $unidades;
        $this->save();
    }

    function getAuditoriaQF_items($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->qf_total_patentes))
            return $this->_getEnteroEnMiles($this->qf_total_items, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->items_rev_qf, $conFormato);
    }
    function setAuditoriaQF_items($items){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->items_rev_qf = $items;
        $this->qf_total_items = $items;
        $this->save();
    }
    // Auditoria Apoyo 1
    function getAuditoriaApoyo1_patentes($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->apoyo1_total_patentes))
            return $this->_getEnteroEnMiles($this->apoyo1_total_patentes, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->ptt_rev_apoyo1, $conFormato);
    }
    function setAuditoriaApoyo1_patentes($ptt){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->ptt_rev_apoyo1 = $ptt;
        $this->apoyo1_total_patentes = $ptt;
        $this->save();
    }
    function getAuditoriaApoyo1_unidades($conFormato=false){
        return $this->_getEnteroEnMiles($this->apoyo1_total_unidades, $conFormato);
    }
    function setAuditoriaApoyo1_unidades($unidades){
        $this->apoyo1_total_unidades = $unidades;
        $this->save();
    }
    function getAuditoriaApoyo1_items($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->apoyo1_total_items))
            return $this->_getEnteroEnMiles($this->apoyo1_total_items, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->items_rev_apoyo1, $conFormato);
    }
    function setAuditoriaApoyo1_items($items){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->items_rev_apoyo1 = $items;
        $this->apoyo1_total_items = $items;
        $this->save();
    }
    // Auditoria Apoyo 2
    function getAuditoriaApoyo2_patentes($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->apoyo2_total_patentes))
            return $this->_getEnteroEnMiles($this->apoyo2_total_patentes, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->ptt_rev_apoyo2, $conFormato);
    }
    function setAuditoriaApoyo2_patentes($ptt){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->ptt_rev_apoyo2 = $ptt;
        $this->apoyo2_total_patentes = $ptt;
        $this->save();
    }
    function getAuditoriaApoyo2_unidades($conFormato=false){
        // solo disponible en el formato nuevo
        return $this->_getEnteroEnMiles($this->apoyo2_total_unidades, $conFormato);
    }
    function setAuditoriaApoyo2_unidades($unidades){
        $this->apoyo2_total_unidades = $unidades;
        $this->save();
    }
    function getAuditoriaApoyo2_items($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->apoyo2_total_items))
            return $this->_getEnteroEnMiles($this->apoyo2_total_items, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->items_rev_apoyo2, $conFormato);
    }
    function setAuditoriaApoyo2_items($items){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->apoyo2_total_items = $items;
        $this->items_rev_apoyo2 = $items;
        $this->save();
    }

    // Auditoria Supervisor
    function getAuditoriaSupervisor_patentes($conFormato=false){
        // el dato puede estar en dos campos distintos dependiendo de la version
        if(isset($this->supervisor_total_patentes))
            return $this->_getEnteroEnMiles($this->supervisor_total_patentes, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->ptt_rev_supervisor_fcv, $conFormato);
    }
    function setAuditoriaSupervisor_patentes($ptt){
        // el dato puede estar en dos campos distintos dependiendo de la version
        $this->ptt_rev_supervisor_fcv = $ptt;
        $this->supervisor_total_patentes = $ptt;
        $this->save();
    }
    function getAuditoriaSupervisor_unidades($conFormato=false){
        // solo disponible en nueva version de acta
        return $this->_getEnteroEnMiles($this->supervisor_total_unidades, $conFormato);
    }
    function setAuditoriaSupervisor_unidades($unidades){
        $this->supervisor_total_unidades = $unidades;
        $this->save();
    }

    function getAuditoriaSupervisor_items($conFormato=false){
        // solo disponible en nueva version de acta
        return $this->_getEnteroEnMiles($this->supervisor_total_items, $conFormato);
    }
    function setAuditoriaSupervisor_items($items){
        // solo disponible en nueva version de acta
        $this->supervisor_total_items = $items;
        $this->save();
    }

    // Correciones Auditoria FCV a SEI
    function getCorreccionPatentesEnAuditoria($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud4, $conFormato);
    }
    function setCorreccionPatentesEnAuditoria($ptt){
        $this->aud4 = $ptt;
        $this->save();
    }
    function getCorreccionItemsEnAuditoria($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud5, $conFormato);
    }
    function setCorreccionItemsEnAuditoria($items){
        $this->aud5 = $items;
        $this->save();
    }
    function getCorreccionUnidadesNetoEnAuditoria($conFormato=false){
        return $this->_getEnteroEnMiles($this->aud6, $conFormato);
    }
    function setCorreccionUnidadesNetoEnAuditoria($unidades){
        $this->aud6 = $unidades;
        $this->save();
    }
    function getCorreccionUnidadesAbsolutasEnAuditoria($conFormato=false){
        return $this->_getEnteroEnMiles($this->unid_absoluto_corregido_auditoria, $conFormato);
    }
    function setCorreccionUnidadesAbsolutasEnAuditoria($unidades){
        $this->unid_absoluto_corregido_auditoria = $unidades;
        $this->save();
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
        return $this->_getPorcentaje($porcentaje, $conFormato);
    }
    function getPorcentajeErrorQF($conFormato=false){
        return $this->_getPorcentaje($this->porcentaje_error_qf, $conFormato);
    }
    function setPorcentajeErrorQF($porcentaje){
        $this->porcentaje_error_qf = $porcentaje;
        $this->save();
    }

    // Variación Grilla
    function getPorcentajeVariacionGrilla($conFormato=false){
        return $this->_getPorcentaje($this->porcentaje_variacion_ajuste_grilla, $conFormato);
    }
    function setPorcentajeVariacionGrilla($porcentaje){
        $this->porcentaje_variacion_ajuste_grilla = $porcentaje;
        $this->save();
    }

    // OTROS
    function getPatentesInventariadas($conFormato=false){
        // la variable puede estar en dos campos distintos
        if($this->ptt_inventariadas)
            return $this->_getEnteroEnMiles($this->ptt_inventariadas, $conFormato);
        else
            return $this->_getEnteroEnMiles($this->tot2, $conFormato);
    }
    function setPatentesInventariadas($patentes){
        $this->ptt_inventariadas = $patentes;
        $this->tot2 = $patentes;
        $this->save();
    }
    function getItemTotalInventariados($conFormato=false){
        return $this->_getEnteroEnMiles($this->total_items_inventariados, $conFormato);
    }
    function getSkuUnicosInventariados($conFormato=false){
        return $this->_getEnteroEnMiles($this->sku_unicos_inventariados, $conFormato);
    }
    function setSkuUnicosInventariados($sku){
        $this->sku_unicos_inventariados = $sku;
        $this->save();
    }

    function getItemRevisadosCliente($conFormato=false){
        // esteban lee el dato: "item_revisado"
        $totalRevisados = $this->items_rev_qf + $this->items_rev_apoyo1 + $this->items_rev_apoyo2;
        return $conFormato? number_format($totalRevisados, 0, ',', '.') : $totalRevisados;
    }
    function getPorcentajeRevisionCliente($conFormato=false){
        $totalContados = $this->getItemTotalInventariados();
        $revisados = $this->getItemRevisadosCliente();
        // si no estan los datos, no se puede hacer el calculo
        if($revisados==null || $totalContados==null)
            return null;
        $porcentaje = $revisados/$totalContados * 100;
        return $conFormato? number_format($porcentaje, 1)."%" : $porcentaje;
    }

    function leerFinProcesoDesdeChecklist(){
        // si la fecha de fin de proceso ya fue leida, entonces no hacer este proceso
        if( $this->getFinProceso()!=null )
            return 'la fecha ya ha sido fijada';

        $zip =  new \ZipArchive();
        $zipPath = $this->archivoFinal->getFullPath();
        $resultado = $zip->open($zipPath);
        if( $resultado !== true )
            return 2;// ocurrio un error

        $checklist1 = $zip->statName('CHECKLIST.pdf');
        $checklist2 = $zip->statName('CHECKLIST_FCV.pdf');
        $checklist1_time = $checklist1['mtime'];
        $checklist2_time = $checklist2['mtime'];

        // el archivo "CHECKLIST.pdf" no siempre se encuentra
        if($checklist1_time!=null){
            $datetime1 = date("Y-m-d H:i:s", $checklist1_time);
            $this->setFinProceso($datetime1);
            $this->save();
            return $datetime1;
        }else if($checklist2_time!=null){
            $datetime2 = date("Y-m-d H:i:s", $checklist2_time);
            $this->setFinProceso($datetime2);
            $this->save();
            return $datetime2;
        }else{
            return 'checklist no encontrado';
        }
    }

    function leerPatentesInventariadasDesdeElZip(){
        // hay tres nombres para el mismo archivo...
        $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO.csv', ';');
        if(isset($unzip->error)){
            $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FCV.csv', ';');
            if(isset($unzip->error)){
                $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FARMA.csv', ';');
                if(isset($unzip->error))
                    return $unzip->error;
            }
        }

        $data = \CSVReader::csv_to_array($unzip->fullpath, ';');
        // extraer los datos de la columna D(index 3), y omitir cualquier campo que no sea un numero
        $column = \CSVReader::getColumn($data, 3);
        $ptt = collect($column)->unique()->filter(function($value){
            return is_numeric($value);
        })->count();

        $this->setPatentesInventariadas($ptt);
        return $ptt;
    }

    function leerFinProcesoDesdeElZip(){
        $this->setFinProceso(null);

        // hay tres nombres para el mismo archivo...
        $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO.csv', ';');
        if(isset($unzip->error)){
            $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FCV.csv', ';');
            if(isset($unzip->error)){
                $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FARMA.csv', ';');
                if(isset($unzip->error)){
                    return $unzip->error;
                }
            }
        }

        // Formato 1: El dato de fecha puede estar en la columna J (index 9) como "20160805222738"
        //                            | YYYY |       MM      |    DD   |           HH        |     MM  |   SS    |
        $regAnnoMesDiaHoraMinSeg = '/^201[0-9](0[1-9]|1[0-2]])[0-3][0-9](0[1-9]|1[0-9]|2[0-4])[0-6][0-9][0-6][0-9]$/';

        // Formato 2: la columna J (index 9) con el formato "20161011" y en la columna K (index 10) como "212518"
        //                  | YYYY |       MM      |    DD   |
        $regAnnoMesDia = '/^201[0-9](0[1-9]|1[0-2])[0-3][1-9]$/';
        //                 |           HH        |     MM  |   SS    |
        $regHoraMinSeg = '/^(0[1-9]|1[0-9]|2[0-4])[0-6][0-9][0-6][0-9]$/';

        $data = \CSVReader::csv_to_array($unzip->fullpath, ';');
        if(!isset($data[0][3]))
            return "error leyendo el array... $this->idActaFCV";

        $finProceso = collect($data)
            // se busca la patente numero 10.000
            ->filter(function($row){
                return isset($row[3]) && $row[3]=="10000";
            })
            ->map(function($row) use ($regAnnoMesDiaHoraMinSeg, $regAnnoMesDia, $regHoraMinSeg) {
                // "formato 1"?
                if (preg_match($regAnnoMesDiaHoraMinSeg, $row[9])) {
                    return $row[9];
                }
                // "formato 2"? probado!
                if (preg_match($regAnnoMesDia, $row[9]) && preg_match($regHoraMinSeg, $row[10])) {
                    return $row[9] . $row[10];
                }
                return null;
            })
            // de todos, busca el con el valor mas alto (el ultimo en revisarse)
            ->max();

        // si no se encontro una patente 10.000 con los campos con fecha / fecha+hora, entonces terminar la busqueda
        if($finProceso==null){
            return null;
        }
        try{
            $datetime = Carbon::createFromFormat('YmdHis', $finProceso)->toDateTimeString();
            $this->setFinProceso($datetime);
            return $datetime;
        }catch(InvalidArgumentException $e){
            return null;
        }
    }

    function leerSkuUnicosDesdeElZip(){
        if( $this->getSkuUnicosInventariados()!=null )
            return 'los sku unicos ya estan en el arhivo';

        // hay tres nombres para el mismo archivo...
        $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO.csv', ';');
        if(isset($unzip->error)){
            $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FCV.csv', ';');
            if(isset($unzip->error)){
                $unzip = $this->archivoFinal->unzipArchivo('CAPTURA_INVENTARIO_ESTANDAR_PUNTO_FARMA.csv', ';');
                if(isset($unzip->error)){
                    return $unzip->error;
                }
            }
        }

        $data = \CSVReader::csv_to_array($unzip->fullpath, ';');
        // extraer los datos de la columna A(index 0), y omitir cualquier campo que no sea un numero
        $column = \CSVReader::getColumn($data, 0);
        $skuUnicos = collect($column)
            ->unique()
            ->filter(function($value){
                return is_numeric($value);
            })
            ->count();

        $this->setSkuUnicosInventariados($skuUnicos);
        return $skuUnicos;
    }

    function leerXXXXXXXX(){
        //$this->total_items_inventariados
    }

    // ####  Setters
    // #### Formatear respuestas
    // #### Scopes para hacer Querys/Busquedas
    static function formatoEdicionActa($acta){
        if(!$acta) return [];
        return [
            // extras
            'inv_idInventario' => $acta->idInventario,
            'archivo_idArchivo' => $acta->idArchivoFinalInventario,
            'publicadaPor' => $acta->publicadaPor? $acta->publicadaPor->nombreCorto() : '--',
            'fechaPublicacion' => $acta->estaPublicada()? $acta->fecha_publicacion : null,
            'publicada' => $acta->estaPublicada(),
            // hitos importantes
            'fechaTomaInventario' => $acta->getFechaTomaInventario(),
            'cliente' => $acta->getCliente(),
            'ceco' => $acta->getCeco(),
            'supervisor' => $acta->getSupervisor(),
            'qf' => $acta->getQF(),
            'inicioConteo' => $acta->getInicioConteo(false),
            'finConteo' => $acta->getFinConteo(false),
            'finProceso' => $acta->getFinProceso(false),
            // duracion
            'duracionConteo' => $acta->getDuracionConteo(true),
            'duracionRevision' => $acta->getDuracionRevision(true),
            'duracionTotalProceso' => $acta->getDuracionTotalProceso(true),
            // dotacion
            'dotacionPresupuestada' => $acta->getDotacionPresupuestada(),
            'dotacionEfectiva' => $acta->getDotacionEfectiva(),
            // unidades
            'unidadesInventariadas' => $acta->getUnidadesInventariadas(),
            'unidadesTeoricas' => $acta->getUnidadesTeoricas(),
            'unidadesDiferenciaNeto' => $acta->getDiferenciaNeto(),
            'unidadesDiferenciaAbsoluta' => $acta->getDiferenciaAbsoluta(),
            // evaluaciones
            'notaPresentacion' => $acta->getNotaPresentacion(),
            'notaSupervisor' => $acta->getNotaSupervisor(),
            'notaConteo' => $acta->getNotaConteo(),
            'notaPromedio' => $acta->getNotaPromedio(),
            // consolidado auditoria FCV
            'consolidadoPatentes' => $acta->getConsolidadoPatentes(),
            'consolidadoUnidades' => $acta->getConsolidadoUnidades(),
            'consolidadoItems' => $acta->getConsolidadoItems(),
            // auditoria QF
            'auditoriaQFPatentes' => $acta->getAuditoriaQF_patentes(),
            'auditoriaQFUnidades' => $acta->getAuditoriaQF_unidades(),
            'auditoriaQFItems' => $acta->getAuditoriaQF_items(),
            // auditoria Apoyo 1
            'auditoriaApoyo1Patentes' => $acta->getAuditoriaApoyo1_patentes(),
            'auditoriaApoyo1Unidades' => $acta->getAuditoriaApoyo1_unidades(),
            'auditoriaApoyo1Items' => $acta->getAuditoriaApoyo1_items(),
            // auditoria Apoyo 2
            'auditoriaApoyo2Patentes' => $acta->getAuditoriaApoyo2_patentes(),
            'auditoriaApoyo2Unidades' => $acta->getAuditoriaApoyo2_unidades(),
            'auditoriaApoyo2Items' => $acta->getAuditoriaApoyo2_items(),
            // auditoria Supervisor FCV
            'auditoriaSupervisorPatentes' => $acta->getAuditoriaSupervisor_patentes(),
            'auditoriaSupervisorUnidades' => $acta->getAuditoriaSupervisor_unidades(),
            'auditoriaSupervisorItems' => $acta->getAuditoriaSupervisor_items(),
            // Correcciones Auditoría FCV a SEI
            'correccionPatentes' => $acta->getCorreccionPatentesEnAuditoria(),
            'correccionItems' => $acta->getCorreccionItemsEnAuditoria(),
            'correccionUnidadesNeto' => $acta->getCorreccionUnidadesNetoEnAuditoria(),
            'correccionUnidadesAbsolutas' => $acta->getCorreccionUnidadesAbsolutasEnAuditoria(),
            // % Error Aud.
            'porcentajeErrorSEI' => $acta->getPorcentajeErrorSei(true),
            'porcentajeErrorQF' => $acta->getPorcentajeErrorQF(),
            // Variación Grilla
            'porcentajeVariacionGrilla' => $acta->getPorcentajeVariacionGrilla(),
        ];
    }

    // #### Buscar / Filtrar Nominas
    static function buscar($peticion){
        $query =  ActasInventariosFCV::where('fecha_publicacion', '!=', '0000-00-00 00:00:00');

        $fechaInicio = $peticion->fechaInicio;
        if(isset($fechaInicio))
            $query->whereHas('inventario', function($q) use ($fechaInicio) {
                $q->where('fechaProgramada', '>=', $fechaInicio);
            });

        $fechaFin = $peticion->fechaFin;
        if(isset($fechaFin))
            $query->whereHas('inventario', function($q) use ($fechaFin) {
                $q->where('fechaProgramada', '<=', $fechaFin);
            });

        // ordenados por fecha programada del inventario
        $collection = $query->get();

        $orden = $peticion->orden;
        if(isset($orden) && $orden=='desc')
            return $collection->sortByDesc('inventario.fechaProgramada');
        else
            return $collection->sortBy('inventario.fechaProgramada');
    }
}
