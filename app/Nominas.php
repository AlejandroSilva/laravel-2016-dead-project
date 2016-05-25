<?php
namespace App;
use Crypt;
use Illuminate\Database\Eloquent\Model;
use App\EstadoNominas;
class Nominas extends Model {
    // llave primaria
    public $primaryKey = 'idNomina';
    // este modelo tiene timestamps
    public $timestamps = false;
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

    // #### Consultas
    public function usuarioEnDotacion($operador){
        return $this->dotacion()->find($operador->id);
    }
    // #### Scopes
//    public function scopeWithLiderCaptadorDotacion($query){
//        return $query->with([
//            'lider',
//            'captador',
//            'dotacion.roles'
//        ]);
//    }

    // #### Formatear
    static function formatearSimple($nomina){
        return [
            "idNomina" => $nomina->idNomina,
            "idLider" => $nomina->idLider,
            "idSupervisor" => $nomina->idSupervisor,
            "idCaptador1" => $nomina->idCaptador1,
            "horaPresentacionLider" => $nomina->horaPresentacionLider,
            "horaPresentacionEquipo" => $nomina->horaPresentacionEquipo,
            "dotacionAsignada" => $nomina->dotacionAsignada,
            "dotacionCaptador1" => $nomina->dotacionCaptador1,
            "fechaSubidaNomina" => $nomina->fechaSubidaNomina,
            "estado" => EstadoNominas::formatearSimple($nomina->estado)
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
//    static function formatearDotacion($nomina){
//        return [
//            'dotacionTitular' => $nomina->dotacionTitular->map('\App\User::formatearSimplePivotDotacion'),
//            'dotacionReemplazo' => $nomina->dotacionReemplazo->map('\App\User::formatearSimplePivotDotacion')
//        ];
//    }
}