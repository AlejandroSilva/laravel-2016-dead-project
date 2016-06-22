<?php
namespace App;
use Crypt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
// Modelos
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
    public function inventario1() {
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaDia', 'idNomina');
    }
    
    public function inventario2(){
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\Inventarios', 'idNominaNoche', 'idNomina');
    }
    
    public function inventario(){
        // devolver el inventario padre
        return $this->inventario1? $this->inventario1() : $this->inventario2();
    }
    
    public function lider(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idLider');
    }
    public function supervisor(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idSupervisor');
    }
    
    public function captador(){
        //     $this->hasOne('App\Model', 'foreign_key', 'local_key');
        return $this->hasOne('App\User', 'id', 'idCaptador1');
    }
    
    public function dotacion(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\User', 'nominas_user', 'idNomina', 'idUser')
            ->withTimestamps()
            ->withPivot('titular', 'idRoleAsignado');
//            ->join('roles', 'idRoleAsignado', '=', 'roles.id');
//            ->select('drink_id', 'customer_id', 'pivot_customer_got_drink', 'chair.name AS pivot_chair_name');
    }
    
    public function dotacionTitular() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', true)
            ->orderBy('nominas_user.created_at', 'asc');
    }
    
    public function dotacionReemplazo() {
        // operadores ordenados por la fecha de asignacion a la nomina
        return $this->dotacion()
            ->where('titular', false)
            ->orderBy('nominas_user.created_at', 'asc');
    }

    public function estado(){
        //     $this->belongsTo('App\Model', 'foreign_key', 'other_key');
        return $this->hasOne('App\EstadoNominas', 'idEstadoNomina', 'idEstadoNomina');
    }

    public function logs(){
        return $this->hasMany('App\NominaLog', 'idNomina', 'idNomina');
    }

    // #### Acciones
    public function addLog($titulo, $texto, $importancia=1, $mostrarAlerta=false){
        $this->logs()->save( new NominaLog([
            'idNomina' => $this->idNomina,
            'titulo' => $titulo,
            'texto' => $texto,
            'importancia' => $importancia,
            'mostrarAlerta' => $mostrarAlerta
        ]) );
    }

    // Utilizar este metodo para cambiar la dotacion (si la dotacion cambia, agregar un registro Log al historia
    public function actualizarDotacionTotal($total, $mostrarAlerta=false){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $total_original = $this->dotacionTotal;
        if($total != $total_original){
            $this->dotacionTotal = $total;
            $this->save();
            $this->addLog('Dotación Total cambio', "Cambio desde $total_original a $this->dotacionTotal", 1, $mostrarAlerta);
        }
    }
    public function actualizarDotacionOperadores($operadores, $mostrarAlerta=false){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $operadores_original = $this->dotacionOperadores;
        if($operadores!= $operadores_original){
            $this->dotacionOperadores = $operadores;
            $this->save();
            $this->addLog('Dotación Total cambio', "Cambio desde $operadores_original a $this->dotacionOperadores", 1, $mostrarAlerta);
        }
    }
    public function actualizarHabilitada($habilitada, $mostrarAlerta=false){
        // Solo si hay un cambio se actualiza y se registra el cambio
        $habilitada_original = $this->habilitada;
        if($habilitada!= $habilitada_original){
            $this->habilitada = $habilitada;
            $this->save();
            if($this->habilitada==true)
                $this->addLog('Nómina activa', "El turno ha cambiado y la nómina se ha vuelto activa", 1, $mostrarAlerta);
            else
                $this->addLog('Nómina inactiva', "El turno ha cambiado y la nómina se ha vuelto inactiva", 1, $mostrarAlerta);
        }
    }

    // #### Helpers / Getters
    public function usuarioEnDotacion($operador){
        return $this->dotacion()->find($operador->id);
    }
    public function horaPresentacionLiderF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaPresentacionLider);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    public function horaPresentacionEquipoF(){
        // Ejemplo: convertir "21:30:00" -> "21:30 hrs."
        $carbon = Carbon::parse($this->horaPresentacionEquipo);
        $minutes = $carbon->minute < 10? "0$carbon->minute" : $carbon->minute;
        return "$carbon->hour:$minutes hrs.";
    }
    public function tieneDotacionCompleta(){
        $supervisor = $this->supervisor? 1 : 0;
        $dotacionTitulares = $this->dotacionTitular()->count();

        if($this->inventario->local->cliente->idCliente!=3){
            // en todos los clientes, solo los operadores cuentan
            // entonces se revisa que la dotacionTitular sea menor a dotacionOperadores
            return ($dotacionTitulares + $supervisor) >= $this->dotacionOperadores;
        }else{
            // Excepto en el cliente CKY, donde el lider tambien cuenta
            return ($dotacionTitulares + $supervisor) >= ($this->dotacionOperadores - 1);
        }
    }

    // #### Scopes para hacer Querys
    public function scopeFechaProgramadaEntre($query, $fechaInicio, $fechaFin){
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
    public function scopeHabilitada($query, $habilitada=true){
        $query->where('habilitada', $habilitada);
        
    }

    // #### Formatear
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
            "fechaSubidaNomina" => $nomina->fechaSubidaNomina,
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
//    static function formatearDotacion($nomina){
//        return [
//            'dotacionTitular' => $nomina->dotacionTitular->map('\App\User::formatearSimplePivotDotacion'),
//            'dotacionReemplazo' => $nomina->dotacionReemplazo->map('\App\User::formatearSimplePivotDotacion')
//        ];
//    }
}