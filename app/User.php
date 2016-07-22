<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable {
    // This will enable the relation with Role and add the following methods roles(), hasRole($name),
    // can($permission), and ability($roles, $permissions, $options) within your User model.
    use EntrustUserTrait;
    
//    protected $table = 'users';     // table name
//    public $primaryKey = 'id';      // llave primaria
//    public $timestamps = true;      // este modelo tiene timestamps
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuarioRUN', 'usuarioDV', 'email', 'emailPersonal',
        'nombre1', 'nombre2', 'apellidoPaterno', 'apellidoMaterno',
        'fechaNacimiento', 'telefono', 'telefonoEmergencia', 'direccion', 'cutComuna',
        'tipoContrato', 'fechaInicioContrato', 'fechaCertificadoAntecedentes', 'banco', 'tipoCuenta', 'numeroCuenta'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function comuna(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Comunas', 'cutComuna', 'cutComuna');
    }

    public function nomasEnLasQueHaParticipado(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\Nominas', 'nominas_user', 'idUser', 'idNomina')
            ->withTimestamps()
            ->withPivot('titular', 'idRoleAsignado');
    }

    public function nombreCorto(){
        return "$this->nombre1 $this->apellidoPaterno";
    }
    public function nombreCompleto(){
        return "$this->nombre1 $this->nombre2 $this->apellidoPaterno $this->apellidoMaterno";
    }

    // #### Formatear
    static function formatearMinimo($user){
        return [
            'id' => $user->id,
            'usuarioRUN' => $user->usuarioRUN,
            'usuarioDV' => $user->usuarioDV,
            'nombre' => $user->nombreCompleto(),
            'roles' => $user->roles->map(function($role){
                return $role->id;
            })
        ];
    }
    
    static function formatearSimple($user){
        if(!$user)
            return null;
        return [
            'id' => $user->id,
            'usuarioRUN' => $user->usuarioRUN,
            'usuarioDV' => $user->usuarioDV,
            'nombre' => $user->nombreCompleto(),
            'nombre1' => $user->nombre1,
            'nombre2' => $user->nombre2,
            'apellidoPaterno' => $user->apellidoPaterno,
            'apellidoMaterno' => $user->apellidoMaterno,
            'imagenPerfil' => $user->imagenPerfil,
            // si "roles" es un arreglo vacio, array_map lanza un error
            //            'roles' => sizeof($user['roles'])>0?
            //                array_map(function($role){
            //                    return $role['name'];
            //                }, $user['roles'])
            //                :
            //                []
            'roles' => $user->roles->map(['\App\Role', 'darFormatoSimple'])
        ];
    }
    
    static function formatearSimplePivotDotacion($user){
        $userArray = User::formatearSimple($user);
        // informacion del pivot generado al unir un usuario con una dotacion
        $userArray['idRoleAsignado'] = $user->pivot->idRoleAsignado;
        //          WIP, ESTO NO FUNCIONABA
        //        $userArray['roleAsignado'] = $user->pivot;
        return $userArray;
    }

    static function formatoCompleto($user){
        $userArray = User::formatearSimple($user);
        $userArray['telefono'] = $user->telefono;
        $userArray['telefonoEmergencia'] = $user->telefonoEmergencia;
        $userArray['fechaNacimiento'] = $user->fechaNacimiento;
        $userArray['email'] = $user->email;
        $userArray['emailPersonal'] = $user->emailPersonal;
        // Contrato
        $userArray['tipoContrato'] = $user->tipoContrato;
        $userArray['fechaInicioContrato'] = $user->fechaInicioContrato;
        $userArray['fechaCertificadoAntecedentes'] = $user->fechaCertificadoAntecedentes;
        // Datos bancarios
        $userArray['banco'] = $user->banco;
        $userArray['tipoCuenta'] = $user->tipoCuenta;
        $userArray['numeroCuenta'] = $user->numeroCuenta;
        // Direccion
        $userArray['direccion'] = $user->direccion;
        $userArray['cutComuna'] = $user->cutComuna;
        $userArray['comuna'] = $user->comuna->nombre;
        $userArray['provincia'] = $user->comuna->provincia->nombre;
        $userArray['region'] = $user->comuna->provincia->region->nombre;
        return $userArray;
    }

    static function formatoTablaMantenedorPersonal($user){
        return [
            'id' => $user->id,
            'RUN' => "$user->usuarioRUN-$user->usuarioDV",
            'nombre1' => $user->nombre1,
            'nombre2' => $user->nombre2,
            'apellidoPaterno' => $user->apellidoPaterno,
            'apellidoMaterno' => $user->apellidoMaterno,
            'fechaNacimiento' => $user->fechaNacimiento=='0000-00-00'? '' : $user->fechaNacimiento,
            //'cutComuna' => $user->cutComuna,
            'comuna' => $user->comuna->nombre,
            'email' => $user->email,
            'telefono' => $user->telefono,
            'bloqueado' => $user->bloqueado=="1",
        ];
    }
}