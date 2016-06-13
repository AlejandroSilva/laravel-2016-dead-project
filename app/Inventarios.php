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
    public function local(){
        //return $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->belongsTo('App\Locales', 'idLocal', 'idLocal');
    }

    public function jornada(){
        //return $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\Jornadas', 'idJornada', 'idJornada');
    }

    public function nominaDia(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaDia');
        return $this->belongsTo('App\Nominas', 'idNominaDia', 'idNomina');
    }
    public function nominaNoche(){
//        return $this->hasOne('App\Nominas', 'idNomina', 'idNominaNoche');
        return $this->belongsTo('App\Nominas', 'idNominaNoche', 'idNomina');
    }

    // Consultas y Calulos
    public function fechaProgramadaF(){
        setlocale(LC_TIME, 'es_CL.utf-8');
        // fecha con formato: ejemplo: "2016-05-30" -> "lunes 30 de mayo, 2016"
        return Carbon::parse($this->fechaProgramada)->formatLocalized('%A %e de %B, %Y');
    }
    public function dotacionTotalSugerido($stock = null){
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
    public function dotacionOperadoresSugerido($stock = null){
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

    // #### With scopes
    public function scopeWithTodo($query){
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ]);
    }
    public function scopeWithClienteFormatoRegion($query) {
        $query->with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region'
        ]);
    }

    // #### Acciones
    public function actualizarFechaProgramada($fechaProgramada){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $fecha_original = $this->fechaProgramada;
        if($fecha_original!= $fechaProgramada){
            // si la fecha no es valida, no hacer nada...
            if( !$this->fecha_valida($fechaProgramada) )
                return;

            $this->fechaProgramada = $fechaProgramada;
            $this->save();

            // Agregar al Log de las nominas la actualizacion de la fecha programada (no mostrar alerta por cambio de stock)
            $this->nominaDia->addLog(  'La Fecha Programada cambio', "Desde $fecha_original a $this->fechaProgramada", 10);
            $this->nominaNoche->addLog('La Fecha Programada cambio', "Desde $fecha_original a $this->fechaProgramada", 10);
        }
    }

    public function actualizarJornada($idJornada){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $jornada_original = $this->idJornada;
        if($jornada_original!= $idJornada){
            $this->idJornada = $idJornada;
            $this->save();

            // cambiar el estado (habilitada) de las nominas, (tambien genera el log)
            $this->nominaDia->actualizarHabilitada( $idJornada==2||$idJornada==4 );      // "dia"(2), o "dia y noche"(4)
            $this->nominaNoche->actualizarHabilitada( $idJornada==3||$idJornada==4 );    // "noche"(3), o "dia y noche"(4)
        }
    }

    // se llama luego de se actualiza el stock de un Local, entonces se recalcula la dotacion de todos los inventarios
    public function actualizarStock($stock, $fechaStock){
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
            $alertarCambio = true;        // alertar si existe el cambio ya que este es "automatico"
            // actualizar la dotaion de ambas, incluso si la nomina no esta visible
            $this->nominaDia->actualizarDotacionTotal($this->dotacionTotalSugerido(), $alertarCambio);
            $this->nominaDia->actualizarDotacionOperadores($this->dotacionOperadoresSugerido(), $alertarCambio);
            $this->nominaNoche->actualizarDotacionTotal($this->dotacionTotalSugerido(), $alertarCambio);
            $this->nominaNoche->actualizarDotacionOperadores($this->dotacionOperadoresSugerido(), $alertarCambio);

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

    // #### Formatear
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



    /**
     * helpers privados
     */
    //Function para validar que la fecha sea valida
    private function fecha_valida($fechaProgramada){
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
}
