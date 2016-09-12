<?php
namespace App;
use Crypt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
// Modelos
use App\DiasHabiles;
use App\EstadoNominas;
use App\NominaLog;

class Nominas extends Model {
    // llave primaria
    public $primaryKey = 'idNomina';
    // este modelo NO tiene timestamps
    public $timestamps = false;
    // campos asignables
    protected $fillable = ['dotacionTotal', 'dotacionOperadores'];

    // #### Relaciones
    function inventario1() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaDia', 'idNomina');
    }
    function inventario2(){
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaNoche', 'idNomina');
    }
    function inventario(){
        // devolver el inventario padre
        return $this->inventario1? $this->inventario1() : $this->inventario2();
    }
    function lider(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idLider');
    }
    function supervisor(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idSupervisor');
    }
    function captador(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idCaptador1');
    }
    function dotacion(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\User', 'nominas_user', 'idNomina', 'idUser')
            ->withTimestamps()
            ->withPivot('titular', 'idRoleAsignado');
//            ->join('roles', 'idRoleAsignado', '=', 'roles.id');
//            ->select('drink_id', 'customer_id', 'pivot_customer_got_drink', 'chair.name AS pivot_chair_name');
    }
    function captadores(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\User', 'nominas_captadores', 'idNomina', 'idCaptador')
            ->withTimestamps()
            ->withPivot('operadoresAsignados');
        //            ->join('roles', 'idRoleAsignado', '=', 'roles.id');
        //            ->select('drink_id', 'customer_id', 'pivot_customer_got_drink', 'chair.name AS pivot_chair_name');
    }
    function estado(){
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\EstadoNominas', 'idEstadoNomina', 'idEstadoNomina');
    }
    function logs(){
        return $this->hasMany('App\NominaLog', 'idNomina', 'idNomina');
    }

    // #### Helpers
    function dotacionTitular() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', true)
            ->orderBy('nominas_user.created_at', 'asc');
    }
    function dotacionReemplazo() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', false)
            ->orderBy('nominas_user.created_at', 'asc');
    }
    function usuarioEnDotacion($operador){
        return $this->dotacion()->find($operador->id);
    }
    function tieneDotacionCompleta(){
        $supervisor = $this->supervisor? 1 : 0;
        $dotacionTitulares = $this->dotacionTitular()->count();
        return ($dotacionTitulares + $supervisor) >= $this->dotacionOperadores;
    }

    // #### Acciones
    function addLog($titulo, $texto, $importancia=1){
        $this->logs()->save( new NominaLog([
            'idNomina' => $this->idNomina,
            'titulo' => $titulo,
            'texto' => $texto,
            'importancia' => $importancia,
            'mostrarAlerta' => false
        ]) );
    }

    // ####  Getters
    function horaPresentacionLiderF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaPresentacionLider);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    function horaPresentacionEquipoF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaPresentacionEquipo);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    function lideresDisponibles(){
        $turno = $this->turno;
        $inventario = $this->inventario;
        $fechaInventario =  $inventario->fechaProgramada;

        $rolLider = \App\Role::where('name', 'Lider')->first();
        // obtener todos los lideres
        $_lideres = $rolLider!=null? $rolLider->users : [];

        // quitar los que tengan un inventario en el mismo turno, el mismo dia
        $lideres_collection = collect($_lideres);

        // si hay un lider seleccionado, pero no esta en la lista de lideres, se puede deber a que:
        // "es una nomina antigua con usuario/lider desvinculado", este usuario se debe agregar de todas formas a la lista
        $liderNomina = $this->lider;
        if( $liderNomina!=null ){
            $lider_en_lista = $lideres_collection->first(function($key, $lider) use($liderNomina){
                return $lider->id == $liderNomina->id;
            });
            if($lider_en_lista==null){
                $lideres_collection->push( $liderNomina );
            }
        }

        $lideres = $lideres_collection->map(function($lider) use ($fechaInventario, $turno){
            return [
                'nombre' => $lider->nombreCorto(),
                'idUsuario' => $lider->id,
                'fechaInicio' => $fechaInventario,
                'fechaFin' => $fechaInventario,
                'turno' => $turno,
                ///**/          'nominas' => $lider->nominasComoTitular($fechaInventario, $fechaInventario, $turno),
                // un lider esta disponible cuando no esta asignado a otra nomina en el mismo turno, Y cuando es el lider
                // que esta asignado actualmente a la nomina
                'disponible' => $lider->disponibleParaInventario($fechaInventario, $turno) ||
                    $lider->id == $this->idLider
            ];
        });

        // agregar al inicio, el lider "SIN LIDER"
        $lideres->splice(0, 0, [[
            'nombre' => '--',
            'idUsuario' => '', // deberia ser null, pero en el front-end se usa '' como value de la opcion
            'fechaInicio' => $fechaInventario,
            'fechaFin' => $fechaInventario,
            'turno' => $turno,
            'disponible' => true
        ]]);
        return $lideres;
    }

    // ####  Setters
    // Utilizar este metodo para cambiar la dotacion (si la dotacion cambia, agregar un registro Log al historia
    function set_dotacionTotal($total){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $total_original = $this->dotacionTotal;
        if($total != $total_original){
            $this->dotacionTotal = $total;
            $this->save();
            $this->addLog('Cambio de dotación total', "Cambio desde $total_original a $this->dotacionTotal", 1);
        }
    }
    function set_dotacionOperadores($operadores){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $operadores_original = $this->dotacionOperadores;
        if($operadores!= $operadores_original){
            $this->dotacionOperadores = $operadores;
            $this->save();
            $this->addLog('Dotación Total cambio', "Cambio desde $operadores_original a $this->dotacionOperadores", 1);
        }
    }
    function set_habilitada($habilitada){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $habilitada_original = $this->habilitada;
        if($habilitada!= $habilitada_original){
            $this->habilitada = $habilitada;
            $this->save();
            if($this->habilitada==true)
                $this->addLog('Nómina activa', "El turno ha cambiado y la nómina se ha vuelto activa", 1);
            else
                $this->addLog('Nómina inactiva', "El turno ha cambiado y la nómina se ha vuelto inactiva", 1);
        }
    }

    // #### Formatear respuestas
    static function formatearSimple($nomina){
        return [
            "idNomina" => $nomina->idNomina,
            "idLider" => $nomina->idLider,
            "idSupervisor" => $nomina->idSupervisor,
            "idCaptador1" => $nomina->idCaptador1,
            "horaPresentacionLider" => $nomina->horaPresentacionLider,
            "horaPresentacionLiderF" => $nomina->horaPresentacionLiderF(),
            "horaPresentacionEquipo" => $nomina->horaPresentacionEquipo,
            "horaPresentacionEquipoF" => $nomina->horaPresentacionEquipoF(),
            "dotacionTotal" => $nomina->dotacionTotal,
            "dotacionOperadores" => $nomina->dotacionOperadores,
            "fechaSubidaNomina" => $nomina->fechaSubidaNomina,              // al parece no se ocupa desde que se generan las nominas en esta misma plataforma, revisasr...
            "fechaLimiteCaptador" => $nomina->fechaLimiteCaptador,
            "turno" => $nomina->turno,
            "estado" => EstadoNominas::formatearSimple($nomina->estado),
            "rectificada" => $nomina->rectificada
        ];
    }
    static function formatearSimpleConPublicId($nomina){
        // no en todas las ocaciones se necesita el publicIdNomina, es de 128 bits y puede resultar costoso de descargar
        $nominaArray = Nominas::formatearSimple($nomina);
        $nominaArray['publicIdNomina'] = Crypt::encrypt($nomina->idNomina);
        return $nominaArray;
    }
    static function formatearConLiderSupervisorCaptadorDotacion($nomina){
        $nominaArray = Nominas::formatearSimpleConPublicId($nomina);
        $nominaArray['lider'] =  User::formatearSimple($nomina->lider);
        $nominaArray['supervisor'] =  User::formatearSimple($nomina->supervisor);
        $nominaArray['captador']  =  User::formatearSimple($nomina->captador1);
        $nominaArray['dotacionTitular']  =  $nomina->dotacionTitular->map('\App\User::formatearSimplePivotDotacion');
        $nominaArray['dotacionReemplazo']  =  $nomina->dotacionReemplazo->map('\App\User::formatearSimplePivotDotacion');
        return $nominaArray;
    }
    static function formatearConInventario($nomina){
        $_nomina = Nominas::formatearSimple($nomina);
        $_nomina['inventario'] = Inventarios::formatoClienteFormatoRegion($nomina->inventario);
        return $_nomina;
    }
    // utilizado por: VistaGeneralController@api_vista
    static function formatear_vistaGeneral($nomina){
        return (object) [
            'id' => $nomina->idNomina,
            'turno'=> $nomina->turno,   // no es neceario mostrar 'jornada' del inventario
            // lider, supervisor, y dotacion
            'dotOperadores' => $nomina->dotacionOperadores,
            'dotTotal' => $nomina->dotacionTotal,
            'idLider' => $nomina->idLider,
            'idSupervisor' => $nomina->idSupervisor,
            'idsDotacion' => $nomina->dotacion->map(function($operador){
                return $operador->id;
            }),
            // INVENTARIO
            'fechaProgramada' => $nomina->inventario->fechaProgramada,
            // LOCAL
            'local' => $nomina->inventario->local->numero,
            'comuna' => $nomina->inventario->local->direccion->comuna->nombre,
            //'region' => $nomina->inventario->local->direccion->comuna->provincia->region->numero,
            // CLIENTE
            'cliente'=> $nomina->inventario->local->cliente->nombreCorto,
        ];
    }

    // #### Scopes para hacer Querys/Busquedas
    function scopeFechaProgramadaEntre($query, $fechaInicio, $fechaFin){
        // que la fecha sea MAYOR a la fechaInicio
        $query
            ->where(function($qq) use($fechaInicio){
                $qq
                    ->whereHas('inventario1', function($q) use($fechaInicio){
                        $q->where('fechaProgramada', '>=', $fechaInicio);
                    })
                    ->orWhereHas('inventario2', function($q) use($fechaInicio){
                        $q->where('fechaProgramada', '>=', $fechaInicio);
                    });
            });
        // y la fecha sea MENOR a la fechaFin
        $query
            ->where(function($qq) use($fechaFin) {
                $qq->whereHas('inventario1', function ($q) use ($fechaFin) {
                    $q->where('fechaProgramada', '<=', $fechaFin);
                })->orWhereHas('inventario2', function ($q) use ($fechaFin) {
                    $q->where('fechaProgramada', '<=', $fechaFin);
                });
            });
    }
    function scopeHabilitada($query, $habilitada=true){
        $query->where('habilitada', $habilitada);
    }

    // #### Buscar / Filtrar Nominas
    static function buscar($peticion){
        // todo: agregar la posibilidad de poner mas campos, como: 'estado'

        $query = Nominas::with([]);
        $query->where('habilitada', true);

        // Buscar por Turno: 'Día' o 'Noche'
        if(isset($peticion->turno)){
            $turno = $peticion->turno;
            $query->where('turno', $turno);
        }

        // Buscar por Lider
        if(isset($peticion->idLider)){
            $idLider = $peticion->idLider;
            $query->where('idLider', $idLider);
        }

        // Buscar por Supervisor
        if(isset($peticion->idSupervisor)){
            $idSupervisor = $peticion->idSupervisor;
            $query->where('idSupervisor', $idSupervisor);
        }

        // Buscar por idCaptador1 (no se ha usado, no puedo validar)
//        if(isset($peticion->idCaptador1)){
//            $idCaptador1 = $peticion->idCaptador1;
//            $query->where('idCaptador1', $idCaptador1);
//        }

        // Buscar por Operador
        if(isset($peticion->idOperador)){
            $idOperador = $peticion->idOperador;
            $query
                ->where(function($q) use($idOperador){
                    // $this->dotacion()->find($operador->id);
                    $q
                        ->whereHas('dotacion', function($qq) use($idOperador){
                            // dotacion = tabla 'nominas_user'
                            // $this->dotacion()->find($operador->id);
                            $qq->where('idUser', $idOperador);
                        });
                });
        }

        // Buscar por Fecha de Inicio
        if(isset($peticion->fechaInicio)){
            $fechaInicio = $peticion->fechaInicio;
            $query
                ->where(function($qq) use($fechaInicio){
                    $qq
                        ->whereHas('inventario1', function($q) use($fechaInicio){
                            $q->where('fechaProgramada', '>=', $fechaInicio);
                        })
                        ->orWhereHas('inventario2', function($q) use($fechaInicio){
                            $q->where('fechaProgramada', '>=', $fechaInicio);
                        });
                });
        }

        // Buscar por Fecha de Fin
        if(isset($peticion->fechaFin)){
            $fechaFin = $peticion->fechaFin;
            $query
                ->where(function($qq) use($fechaFin) {
                    $qq->whereHas('inventario1', function ($q) use ($fechaFin) {
                        $q->where('fechaProgramada', '<=', $fechaFin);
                    })->orWhereHas('inventario2', function ($q) use ($fechaFin) {
                        $q->where('fechaProgramada', '<=', $fechaFin);
                    });
                });
        }

        // los resultados se deben ordenar en el metodo controlador que lo llame
        return $query->get();
    }
}