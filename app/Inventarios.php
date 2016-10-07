<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// Modelos
use App\Locales;
use App\ActasInventariosFCV;

class Inventarios extends Model {
    // llave primaria
    public $primaryKey = 'idInventario';
    // este modelo tiene timestamps
    public $timestamps = true;

    // #### Relaciones
    function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }
    function jornada(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\Jornadas', 'idJornada', 'idJornada');
    }
    function nominaDia(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Nominas', 'idNominaDia', 'idNomina');
    }
    function nominaNoche(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Nominas', 'idNominaNoche', 'idNomina');
    }
    function archivosFinales(){
        return $this->hasMany('App\ArchivoFinalInventario', 'idInventario', 'idInventario');
    }

    function actaFCV(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\ActasInventariosFCV', 'idInventario', 'idInventario');
    }

    // #### Helpers
    function dotacionTotalSugerido($stock = null){
        // si no se entrega el stock como parametro, se toma el stock actual del local
        $stock = isset($stock)? $stock : $this->stockTeorico;

        $producion = $this->local->formatoLocal->produccionSugerida;
        // El ultimo stock actualizado / Produccion del tipo de local
        $personasQueCuentan = round($stock/$producion);

        // en todos los clientes, solo los operadores cuentan (total = operadores+lider)
        if($this->local->cliente->idCliente!=3){
            $total = $personasQueCuentan + 1;
        }
        // excepto en el cliente CKY, donde el lider tambien cuenta (total = operadores-1+lider = operadores)
        else{
            $total = $personasQueCuentan;
        }
        // retornar 0 en caso de ser negativo..
        return $total<0? 0 : $total;
    }
    function dotacionOperadoresSugerido($stock = null){
        // si no se entrega el stock como parametro, se toma el stock actual del local
        $stock = isset($stock)? $stock : $this->stockTeorico;

        $producion = $this->local->formatoLocal->produccionSugerida;
        // El ultimo stock actualizado / Produccion del tipo de local
        $personasQueCuentan = round($stock/$producion);

        // en todos los clientes, solo los operadores cuentan
        if($this->local->cliente->idCliente!=3){
            $operadores = $personasQueCuentan;
        }
        // excepto en el cliente CKY, donde el lider tambien cuenta (por lo que se lleva 1 operador menos)
        else{
            $operadores = $personasQueCuentan - 1;
        }
        // retornar 0 en caso de ser negativo..
        return $operadores<0? 0 : $operadores;
    }
    function patentesSugeridas(){
        $idCliente = $this->local->idCliente;
        if($idCliente==2 || $idCliente==5){
            // para el cliente FCV(2) y FSB(5), se calcula PTT=stock/44
            return round($this->stockTeorico/44);
        }else{
            // para los otros clientes, se calcula PTT=stock/110
            return round($this->stockTeorico/110);
        }
    }
    function tieneTopeFechaConAuditoria(){
        // verifica si existe una auditoria programada cerca de este inventario

        // si el dia no esta seleccionado, entonces no buscar el tope de fecha con otras auditorias
        $fecha = explode('-', $this->fechaProgramada);
        $dia = $fecha[2];
        if(!isset($dia) || $dia=='00')
            return null;

        // no se puede hacer una auditoria el mismo dia, o 4 dias habiles despues de un inventario
        $fecha_0diaHabilAntes = DiasHabiles::find($this->fechaProgramada)->diasHabilesAntes(0);
        $fecha_3diasHabilesDespues = DiasHabiles::find($this->fechaProgramada)->diasHabilesDespues(3);

        $auditoriasCercanas = Auditorias::whereRaw("idLocal = $this->idLocal")
            ->whereRaw("fechaProgramada >= '$fecha_0diaHabilAntes->fecha'")
            ->whereRaw("fechaProgramada <= '$fecha_3diasHabilesDespues->fecha'")
            ->get();

        if($auditoriasCercanas->count()>0){
            $fechas = $auditoriasCercanas->map(function($auditoria){
                return $auditoria->fechaProgramada;
            })->toArray();
            $fechas = implode(", ", $fechas);
            return "Auditoria programada para el dia: $fechas";
        } else
            return null;

        // Se Guarda para hacer debug
//        return [
//            'fechaProgramada'=>$this->fechaProgramada,
//            'fechaInicioBusqueda'=>$fecha_0diaHabilAntes->fecha,
//            'fechaFinBusqueda'=>$fecha_3diasHabilesDespues->fecha,
//            'auditorias'=>$auditoriasCercanas,
//            'tieneTopeFechaConAuditoria' => $auditoriasCercanas->count()>0
//        ];
    }
    static function calcularFechaLimiteCaptador($fechaProgramada){
        $cuartoDiasHabilAntes = DiasHabiles::with([])
            // se toman todos los dias habiles ANTERIORES a la fecha de programacion
            ->where('fecha', '<', $fechaProgramada)
            ->where('habil', true)
            ->orderBy('fecha', 'desc')
            // saltar 3 dias habiles y tomar el 4°
            ->skip(3)
            ->take(1)
            ->get()
            ->first();
        return $cuartoDiasHabilAntes->fecha;
    }
    private function _fecha_valida($fechaProgramada){
        $fecha = explode('-', $fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia = $fecha[2];

        // cuando se pone una fecha del tipo '2016-04-', checkdate lanza una excepcion
        if( !isset($anno) || !isset($mes) || !isset($dia)) {
            return false;
        }else{
            return checkdate($mes,$dia,$anno);
        }
    }

    // #### Acciones
    function agregarArchivoFinal($user, $archivo_formulario){
        $ceco = $this->local->numero;
        $cliente = $this->local->cliente->nombreCorto;
        $timestamp = Carbon::now()->format("Y-m-d_h-i-s");
        $extra = "[$timestamp][$cliente][$ceco][$this->fechaProgramada]";
        $carpetaDestino = ArchivoFinalInventario::getPathCarpetaArchivos($cliente);

        // mover el archivo a la carpeta que corresponde
        $archivoFinal = \ArchivosHelper::moverACarpeta($archivo_formulario, $extra, $carpetaDestino);

        // guardar en la BD sus datos
        return ArchivoFinalInventario::create([
            'idInventario' => $this->idInventario,
            'idSubidoPor' => $user? $user->id : null,
            'nombre_archivo' => $archivoFinal->nombre_archivo,
            'nombre_original' => $archivoFinal->nombre_original,
            'actaValida' => false,
            'resultado' => 'ACTA PENDIENTE DE PROCESAR'
        ]);
    }
    function insertarOActualizarActa($datosActa, $idArchivoFinalInventario){
        // agregar el archivo desde el cual se cargaron los datos
        $datosActa['idArchivoFinalInventario'] = $idArchivoFinalInventario;
        $datosActa['idInventario'] = $this->idInventario;

        // si ya existe un acta para este inventario, se actualizan los datos, de lo contrario se crea
        $acta = ActasInventariosFCV::where('idInventario', $this->idInventario)->first();
        if($acta){
            $acta->update($datosActa);
        }else
            ActasInventariosFCV::create($datosActa);
    }

    // #### Getters
    function fechaProgramadaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        $fecha = explode('-', $this->fechaProgramada);
        $anno = $fecha[0];
        $mes  = $fecha[1];
        $dia  = $fecha[2];

        // si no esta seleccionado el dia, se muestra con otro formado
        if($dia==0){
            // fecha con formato: ejemplo: "2016-05-00" -> "mayo, 2016"
            // FIX: si se entrega 2016-02-01, el formato indica "enero, 2016, por eso se genera una fecha con dia "1"
            return Carbon::parse( "$anno-$mes-01")->formatLocalized('%B, %Y');
        }else{
            // fecha con formato: ejemplo: "2016-05-30" -> "lunes 30 de mayo, 2016"
            return Carbon::parse($this->fechaProgramada)->formatLocalized('%A %e de %B, %Y');
        }
    }
    function estadoArchivoFinal(){
        // si no hay nomina: esta pendiente
        $acta = $this->actaFCV;
        if(!$acta)
            return 'pendiente';
        return $acta->estaPublicada()? 'publicado' : 'por publicar';
    }

    // #### Setters
    function set_fechaProgramada($fechaProgramada){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $fecha_original = $this->fechaProgramada;
        if($fecha_original!= $fechaProgramada){
            // si la fecha no es valida, no hacer nada...
            if( !$this->_fecha_valida($fechaProgramada) )
                return;

            $this->fechaProgramada = $fechaProgramada;
            $this->save();

            // Agregar al Log de las nominas la actualizacion de la fecha programada (no mostrar alerta por cambio de stock)
            $this->nominaDia->addLog(  'La Fecha Programada cambio', "Desde $fecha_original a $this->fechaProgramada", 10);
            $this->nominaNoche->addLog('La Fecha Programada cambio', "Desde $fecha_original a $this->fechaProgramada", 10);

            // cuando se actualiza la fecha programada, tambien se acutaliza la fecha limite para que el Captador envie la nomina
            $this->nominaDia->fechaLimiteCaptador = Inventarios::calcularFechaLimiteCaptador($this->fechaProgramada);
            $this->nominaDia->save();
            $this->nominaNoche->fechaLimiteCaptador = Inventarios::calcularFechaLimiteCaptador($this->fechaProgramada);
            $this->nominaNoche->save();
        }
    }
    function set_jornada($idJornada){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $jornada_original = $this->idJornada;
        if($jornada_original!= $idJornada){
            $this->idJornada = $idJornada;
            $this->save();

            // cambiar el estado (habilitada) de las nominas, (tambien genera el log)
            $this->nominaDia->set_habilitada( $idJornada==2||$idJornada==4 );      // "dia"(2), o "dia y noche"(4)
            $this->nominaNoche->set_habilitada( $idJornada==3||$idJornada==4 );    // "noche"(3), o "dia y noche"(4)
        }
    }
    // se llama luego de se actualiza el stock de un Local, entonces se recalcula la dotacion de todos los inventarios
    function set_stock($stock, $fechaStock){
        $habDia= $this->nominaDia->habilitada;
        $habNoche = $this->nominaNoche->habilitada;
        $estadoDia = $this->nominaDia->estado;
        $estadoNoche = $this->nominaNoche->estado;

        // si una de las dos nominas (la de dia o de noche) esta pendiente, entoncse se puede cambiar el stock
        if( ($habDia&&$estadoDia->idEstadoNomina==2) || ($habNoche&&$estadoNoche->idEstadoNomina==2) ){

            // actualizar el stock del inventario
            $stock_original = $this->stockTeorico;
            if($stock!= $stock_original){
                $this->stockTeorico = $stock;
                $this->fechaStock = $fechaStock;
                $this->save();

                // Agregar al Log de las nominas la actualizacion del stock (no mostrar alerta por cambio de stock)
                $this->nominaDia->addLog(  'El stock cambio', "Desde $stock_original a $this->stockTeorico", 1);
                $this->nominaNoche->addLog('El stock cambio', "Desde $stock_original a $this->stockTeorico", 1);
            }

            // recalcular la dotacion de las nominas,
            // actualizar la dotaion de ambas, incluso si la nomina no esta visible
            $this->nominaDia->set_dotacionTotal($this->dotacionTotalSugerido());
            $this->nominaDia->set_dotacionOperadores($this->dotacionOperadoresSugerido());
            $this->nominaNoche->set_dotacionTotal($this->dotacionTotalSugerido());
            $this->nominaNoche->set_dotacionOperadores($this->dotacionOperadoresSugerido());

            return [
                'fechaProgramada' => $this->fechaProgramadaF(),
                'estado' => "stock, fechaStock y dotacion actualizados ($stock al $fechaStock)",
                'error' => ''
            ];
        }else{
            return [
                'fechaProgramada' => $this->fechaProgramadaF(),
                'estado'=>'',
                'error'=>'El inventario no esta Pendiente'
            ];
        }
    }

    // #### Formatear respuestas
    static function formatoSimple($inventario){
        return [
            'idInventario' => $inventario->idInventario,
            'idJornada' => $inventario->idJornada,
            'jornada' => $inventario->jornada->nombre,
            'inventario_fechaProgramada' => $inventario->fechaProgramada,
            'inventario_fechaProgramadaF' => $inventario->fechaProgramadaF(),
            'inventario_stockTeorico' => $inventario->stockTeorico,
            'inventario_fechaStock' => $inventario->fechaStock,
            'inventario_dotacionAsignadaTotal' => $inventario->dotacionAsignadaTotal,
        ];
    }
//    static function formatoClienteFormatoRegion($inventario) {
//        $inventarioArray = Inventarios::formatoSimple($inventario);
//        $inventarioArray['local'] = Locales::formatoLocal_completo($inventario->local);
//        return $inventarioArray;
//    }
//    static function formatoClienteFormatoRegion_nominas ($inventario) {
//        $_inventario = Inventarios::formatoSimple($inventario);
//        $_inventario['local'] = Locales::formatoLocal_completo($inventario->local);
//        $_inventario['nominaDia']   = $inventario->nominaDia->habilitada? Nominas::formatearSimple($inventario->nominaDia) : null;
//        $_inventario['nominaNoche'] = $inventario->nominaNoche->habilitada? Nominas::formatearSimple($inventario->nominaNoche) : null;
//        return $_inventario;
//    }
    // formato utilizado en el modulo "Programacion Semanal IG"
    static function formato_programacionIGSemanal($inventario){
        return [
            'inv_idInventario' => $inventario->idInventario,
            'inv_fechaProgramadaF' => $inventario->fechaProgramadaF(),
            'inv_fechaProgramada' => $inventario->fechaProgramada,
            'inv_fechaProgramadaDOW' => DiasHabiles::diaDeLaSemana($inventario->fechaProgramada),
            // dia texto, dia, mes anno separados...
            'cliente_idCliente' => $inventario->local->idCliente,
            'cliente_nombreCorto' => $inventario->local->cliente->nombreCorto,
            'local_idLocal' => $inventario->idLocal,
            'local_ceco' => $inventario->local->numero,
            'local_nombre' => $inventario->local->nombre,
            'local_idFormato' => $inventario->local->idFormatoLocal,
            'local_formatoLocal' => $inventario->local->formatoLocal->nombre,
            'local_produccionSugerida' => $inventario->local->formatoLocal->produccionSugerida,
            'local_comuna' => $inventario->local->direccion->comuna->nombre,
            'local_cutComuna' => $inventario->local->direccion->cutComuna,
            'local_region' => $inventario->local->direccion->comuna->provincia->region->numero,
            'local_cutRegion' => $inventario->local->direccion->comuna->provincia->cutRegion,
            'local_direccion' => $inventario->local->direccion->direccion,
            'local_horaApertura' => $inventario->local->horaApertura,
            'local_horaCierre' => $inventario->local->horaCierre,
            'local_topeFechaConAuditoria' => $inventario->tieneTopeFechaConAuditoria(),

            'inv_idJornada' => $inventario->idJornada,
            'inv_jornada' => $inventario->jornada->nombre,
            'inv_stockTeorico' => $inventario->stockTeorico,
            'inv_fechaStock' => $inventario->fechaStock,

            'inv_patentes' => $inventario->patentesSugeridas(),
            'inv_unidadesReales' => $inventario->unidadesReal,
            'inv_unidadesTeorico' => $inventario->unidadesTeorico,
            'inv_estadoArchivoFinal' => $inventario->estadoArchivoFinal(),
            // ######## NOMINA DIA ########
            'ndia_idNomina' => $inventario->nominaDia->idNomina,
            'ndia_dotTotal' => $inventario->nominaDia->dotacionTotal,
            'ndia_dotOperadores' => $inventario->nominaDia->dotacionOperadores,
            'ndia_idLider' => $inventario->nominaDia->idLider,
            'ndia_lider' => $inventario->nominaDia->lider? $inventario->nominaDia->lider->nombreCorto() : '--',
            'ndia_hrLider' => $inventario->nominaDia->horaPresentacionLider,
            'ndia_hrEquipo' => $inventario->nominaDia->horaPresentacionEquipo,
            'ndia_idSupervisor' => $inventario->nominaDia->idSupervisor,
            'ndia_supervisor' => $inventario->nominaDia->supervisor? $inventario->nominaDia->supervisor->nombreCorto() : '--',
            'ndia_idCaptador1' => $inventario->nominaDia->idCaptador1,
            'ndia_captador1' => $inventario->nominaDia->captador1? $inventario->nominaDia->captador1->nombreCorto() : '--',
            'ndia_captadores' => $inventario->nominaDia->captadores->map(function($captador){
                return [
                    'idUsuario' => $captador->id,
                    'nombre' => $captador->nombreCorto(),
                    'asignados' => $captador->pivot->operadoresAsignados
                ];
            }),
            // estado nomina ** (cambiar proximamente)
            'ndia_idEstadoNomina' => $inventario->nominaDia->idEstadoNomina,
            'ndia_habilitada' => $inventario->nominaDia->habilitada,
            'ndia_urlNominaPago' => $inventario->nominaDia->urlNominaPago,

            // ####### NOMINA NOCHE #######
            'nnoche_idNomina' => $inventario->nominaNoche->idNomina,
            'nnoche_dotTotal' => $inventario->nominaNoche->dotacionTotal,
            'nnoche_dotOperadores' => $inventario->nominaNoche->dotacionOperadores,
            'nnoche_idLider' => $inventario->nominaNoche->idLider,
            'nnoche_lider' => $inventario->nominaNoche->lider? $inventario->nominaNoche->lider->nombreCorto() : '--',
            'nnoche_hrLider' => $inventario->nominaNoche->horaPresentacionLider,
            'nnoche_hrEquipo' => $inventario->nominaNoche->horaPresentacionEquipo,
            'nnoche_idSupervisor' => $inventario->nominaNoche->idSupervisor,
            'nnoche_supervisor' => $inventario->nominaNoche->supervisor? $inventario->nominaNoche->supervisor->nombreCorto() : '--',
            'nnoche_idCaptador1' => $inventario->nominaNoche->idCaptador1,
            'nnoche_captador1' => $inventario->nominaNoche->captador1? $inventario->nominaNoche->captador1->nombreCorto() : '--',
            'nnoche_captadores' => $inventario->nominaNoche->captadores->map(function($captador){
                return [
                    'idUsuario' => $captador->id,
                    'nombre' => $captador->nombreCorto(),
                    'asignados' => $captador->pivot->operadoresAsignados
                ];
            }),
            // estado nomina ** (cambiar proximamente)
            'nnoche_idEstadoNomina' => $inventario->nominaNoche->idEstadoNomina,
            'nnoche_habilitada' => $inventario->nominaNoche->habilitada,
            'nnoche_urlNominaPago' => $inventario->nominaNoche->urlNominaPago,
        ];
    }

    static function formatoActa($inventarios){
        $acta = $inventarios->actaFCV;
        return ActasInventariosFCV::formatoEdicionActa($acta);
    }

    // #### Scopes para hacer Querys/Busquedas
    static function buscar($peticion){
        // todo: deberian mejorar bastante los tiempos de respuesta, si se agregan los eager loading dentro del query
        $query = Inventarios::with([]);

        // Cliente (solo si existe y es dintito a 0)
        $idCliente = $peticion->idCliente;
        if( isset($idCliente) && $idCliente!=0) {
            $query->whereHas('local', function ($q) use ($idCliente) {
                $q->where('idCliente', '=', $idCliente);
            });
        }

        // Fecha desde
        if(isset($peticion->fechaInicio))
            $query->where('fechaProgramada', '>=', $peticion->fechaInicio);

        // Fecha hasta
        if(isset($peticion->fechaFin))
            $query->where('fechaProgramada', '<=', $peticion->fechaFin);

        // Mes
        if(isset($peticion->mes)){
            $_fecha = explode('-', $peticion->mes);
            $anno = $_fecha[0];
            $mes  = $_fecha[1];
            $query
                ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                ->whereRaw("extract(month from fechaProgramada) = ?", [$mes]);
        }

        // Incluir con "fecha pendiente" en el resultado, solo si se indica explicitamente
        $incluirConFechaPendiente = isset($peticion->incluirConFechaPendiente) && $peticion->incluirConFechaPendiente=='true';
        if( $incluirConFechaPendiente==false )
            $query->whereRaw("extract(day from fechaProgramada) != 0");

        $query->orderBy('fechaProgramada', 'asc');
        return $query->get();
    }
}