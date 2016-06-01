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
    public function actualizarStock($stock, $fechaStock){
        $habDia= $this->nominaDia->habilitada;
        $habNoche = $this->nominaNoche->habilitada;
        $estadoDia = $this->nominaDia->estado;
        $estadoNoche = $this->nominaNoche->estado;
        // si la nomina de dia esta pendinete, o la de noche, entoncse cambiar el stock
        if( ($habDia&&$estadoDia->idEstadoNomina==2) || ($habNoche&&$estadoNoche->idEstadoNomina==2) ){
            // actualizar el stock del inventario
            $this->stockTeorico = $stock;
            $this->fechaStock = $fechaStock;
            $this->save();

            // actualizar la dotacion de las nominas
            $this->nominaDia->dotacionTotal = $this->dotacionTotalSugerido();
            $this->nominaDia->dotacionOperadores = $this->dotacionOperadoresSugerido();
            $this->nominaDia->save();
            $this->nominaNoche->dotacionTotal = $this->dotacionTotalSugerido();
            $this->nominaNoche->dotacionOperadores = $this->dotacionOperadoresSugerido();
            $this->nominaNoche->save();

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
        $inventarioArray['local'] = Locales::formatearConClienteFormatoDireccionRegion($inventario->local);
        return $inventarioArray;
    }

    static function formatoClienteFormatoRegion_nominas ($inventario) {
        $_inventario = Inventarios::formatoSimple($inventario);
        $_inventario['local'] = Locales::formatearConClienteFormatoDireccionRegion($inventario->local);
        $_inventario['nominaDia']   = $inventario->nominaDia->habilitada? Nominas::formatearSimple($inventario->nominaDia) : null;
        $_inventario['nominaNoche'] = $inventario->nominaNoche->habilitada? Nominas::formatearSimple($inventario->nominaNoche) : null;
        return $_inventario;
    }
}
