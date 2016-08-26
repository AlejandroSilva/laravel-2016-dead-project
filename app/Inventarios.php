<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
// Modelos
use App\Locales;

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
    function ______patentesSugeridas____sin_uso__(){
        $idCliente = $this->local->idCliente;
        if($idCliente==2 || $idCliente==5){
            // para el cliente FCV(2) y FSB(5), se calcula PTT=stock/44
            return $this->stockTeorico/44;
        }else{
            // para los otros clientes, se calcula PTT=stock/110
            return $this->stockTeorico/110;
        }
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
    //

    // ####  Getters
    function fechaProgramadaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        // fecha con formato: ejemplo: "2016-05-30" -> "lunes 30 de mayo, 2016"
        return Carbon::parse($this->fechaProgramada)->formatLocalized('%A %e de %B, %Y');
    }

    // ####  Setters
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
    static function formatoClienteFormatoRegion($inventario) {
        $inventarioArray = Inventarios::formatoSimple($inventario);
        $inventarioArray['local'] = Locales::formatoLocal_completo($inventario->local);
        return $inventarioArray;
    }
    static function formatoClienteFormatoRegion_nominas ($inventario) {
        $_inventario = Inventarios::formatoSimple($inventario);
        $_inventario['local'] = Locales::formatoLocal_completo($inventario->local);
        $_inventario['nominaDia']   = $inventario->nominaDia->habilitada? Nominas::formatearSimple($inventario->nominaDia) : null;
        $_inventario['nominaNoche'] = $inventario->nominaNoche->habilitada? Nominas::formatearSimple($inventario->nominaNoche) : null;
        return $_inventario;
    }

    // #### 'with' scopes, se usan para agregar campos/relaciones en las respuestas json
    function scopeWithTodo($query){
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.supervisor',
            'nominaNoche.supervisor',
            'nominaDia.captador',
            'nominaNoche.captador',
        ]);
    }
    function scopeWithClienteFormatoRegion($query) {
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region'
        ]);
    }

    // #### Scopes para hacer Querys/Busquedas
    function scopeFechaProgramadaEntre($query, $fechaInicio, $fechaFin){
        // al parecer funciona, hacer mas pruebas
        $query->where('fechaProgramada', '>=', $fechaInicio);
        $query->where('fechaProgramada', '<=', $fechaFin);
    }
    function scopeConCaptador___no_se_ocupa($query, $idCaptador){
        // no probado
        // buscar el captador en la nomina de "dia" O en la de "noche", la nomina debe estar "Habilitada"
        $query
            ->whereHas('nominaDia', function($q) use ($idCaptador){
                $q->where('idCaptador1', $idCaptador)->where('habilitada', true);
            })
            ->orWhereHas('nominaNoche', function($q) use ($idCaptador){
                $q->where('idCaptador1', $idCaptador)->where('habilitada', true);
            });
    }
}