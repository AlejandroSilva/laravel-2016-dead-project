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
    static function calcularTotales($inventarios) {
        $unidadesInventariadas = 0;
        $minutosTrabajados = 0;
        $itemsHH_total = 0;
        $itemsHH_disponible = 0;
        $nota_total = 0;
        $nota_disponible = 0;
        $porcentajeError_total = 0;
        $porcentajeError_disponible = 0;
        $itemsRevisadosCliente = 0;
        $porcentajeRevision_total = 0;
        $porcentajeRevision_disponible = 0;
        $patentesRevisadasCliente_total = 0;
        $diferenciaNeta_total = 0;
        foreach ($inventarios as $inv) {
            $acta = $inv->actaFCV;
            $datosDisponibles = $acta && $acta->estaPublicada();

            if($datosDisponibles){
                $unidadesInventariadas += $acta->getUnidadesInventariadas();
                $minutosTrabajados += $acta->getHorasTrabajadas();
                $itemsRevisadosCliente = $acta->getItemRevisadosCliente();
                $patentesRevisadasCliente_total += $acta->getPatentesRevisadasTotales();
                $diferenciaNeta_total += $acta->getDiferenciaNeta();
            }
            // promediar solo si los datos estan disponibles y existen dentro del acta
            if($datosDisponibles && $acta->getItemsHH()) {
                $itemsHH_total += $acta->getItemsHH();
                $itemsHH_disponible += 1;
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
        $itemsHH_promedio = $itemsHH_disponible>0? round($itemsHH_total/$itemsHH_disponible) : '';
        $notas_promedio = $nota_disponible>0? $nota_total/$nota_disponible : '';
        $porcentajeError_promedio = $porcentajeError_disponible>0? number_format($porcentajeError_total/$porcentajeError_disponible, 1, ',', '.').'%' : '';
        $porcentajeRevision_promedio = $porcentajeRevision_disponible>0? number_format($porcentajeRevision_total/$porcentajeRevision_disponible, 1, ',', '.').'%' : '';
        return (object)[
            'unidadesInventariadas' => number_format($unidadesInventariadas, 0, ',', '.'),
            'horasTrabajadas' => gmdate('H:i:s', $minutosTrabajados),
            'itemsHH_promedio' => number_format($itemsHH_promedio, 0, ',', '.'),
            'nota_promedio' => number_format($notas_promedio, 1, ',', '.'),
            'porcentajeError_promedio' => $porcentajeError_promedio,
            'itemsRevisadosCliente' => number_format($itemsRevisadosCliente, 0, ',', '.'),
            'porcentajeRevisionCliente_promedio' => $porcentajeRevision_promedio,
            'patentesRevisadasCliente_total' => number_format($patentesRevisadasCliente_total , 0, ',', '.'),
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
    function getHorasTrabajadas($conFormato=false){
        $inicio = $this->getInicioConteo();
        $fin = $this->getFinConteo();
        // fechas validas?
        if($inicio==null || $fin==null)
            return null;

        $td_inicio = Carbon::parse($inicio);
        $td_fin = Carbon::parse($fin);

        $diferencia = $td_fin->diffInSeconds($td_inicio);
        return $conFormato? gmdate('H:i:s', $diferencia) : $diferencia;
    }
    function getUnidadesInventariadas($conFormato=false){
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
    function getNotaPromedio($conFormato=false){
        if($this->nota1==0 || $this->nota2==0 || $this->nota3==0)
            return null;
        $promedio = ($this->nota1+$this->nota2+$this->nota3)/3;
        return $conFormato? number_format($promedio, 1, ',', '.') : $promedio;
    }
    function getItemCorregido(){
        return $this->unid_absoluto_corregido_auditoria;
    }
    function getItemRevisadosCliente($conFormato=false){
        // esteban lee el dato: "item_revisado"
        $totalRevisados = $this->items_rev_qf + $this->items_rev_apoyo1 + $this->items_rev_apoyo2;
        return $conFormato? number_format($totalRevisados, 0, ',', '.') : $totalRevisados;
    }
    function getPorcentajeErrorSei($conFormato=false){
        $corregidos = $this->getItemCorregido();
        $revisados = $this->getItemRevisadosCliente();
        // si no estan los datos, no se puede hacer el calculo
        if($revisados==null || $corregidos==null)
            return null;
        $porcentaje = ($corregidos/$revisados)*100;
        return $conFormato? number_format($porcentaje, 1)."%" : $porcentaje;
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
    function getPatentesRevisadasTotales($conFormato=false){
        if($this->aud1==null)
            return null;
        return $conFormato? number_format($this->aud1, 0, ',', '.') : $this->aud1;
    }
    function getDiferenciaNeta($conFormato=false){
        if($this->aju2==null)
            return null;
        return $conFormato? '$ '.number_format($this->aju2, 0, ',', '.') : $this->aju2;
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
