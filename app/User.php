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

    // The attributes that are mass assignable.
    protected $fillable = [
        'usuarioRUN', 'usuarioDV', 'email', 'emailPersonal',
        'nombre1', 'nombre2', 'apellidoPaterno', 'apellidoMaterno',
        'fechaNacimiento', 'telefono', 'telefonoEmergencia', 'direccion', 'cutComuna',
        'tipoContrato', 'fechaInicioContrato', 'fechaCertificadoAntecedentes', 'banco', 'tipoCuenta', 'numeroCuenta'
    ];
    // The attributes excluded from the model's JSON form.
    protected $hidden = [
        'password', 'remember_token',
    ];

    // #### Relaciones
    function comuna(){
        // belongsTo(modelo, this.fogeignKey, parent.otherKey)
        return $this->belongsTo('App\Comunas', 'cutComuna', 'cutComuna');
    }
    function nominasComoCaptador(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\Nominas', 'nominas_captadores', 'idCaptador', 'idNomina')
            ->withTimestamps()
            ->withPivot('titular', 'idRoleAsignado', 'idCaptador');
    }
    function __nominasComoOperador__sin_uso__(){
        // la relacion entre las dos tablas tiene timestamps (para ordenar), y otros campos
        return $this->belongsToMany('App\Nominas', 'nominas_user', 'idUser', 'idNomina')
            ->withTimestamps()
            ->withPivot('titular', 'idRoleAsignado', 'idCaptador');
    }

    // #### Helpers
    // Busca las nominas en las que ha participado como titular (lider, supervisor, o operador)
    function nominasComoTitular($fechaInicio, $fechaFin, $turno=null){
        // buscar nominas como "lider"
        $nominasLider = \App\Nominas::buscar( (object)[
            'turno' => $turno,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'idLider' => $this->id,
        ])->map(function($nomina){
           $nomina->cargoUsuario = "Líder";
            return $nomina;
        });

        // buscar nominas como "supervisor"
        $nominasSupervisor = \App\Nominas::buscar( (object)[
            'turno' => $turno,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'idSupervisor' => $this->id
        ])->map(function($nomina){
            $nomina->cargoUsuario = "Supervisor";
            return $nomina;
        });

        // buscar nominas como "operador"
        $nominasOperador = \App\Nominas::buscar( (object)[
            'turno' => $turno,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'idOperador' => $this->id
        ])->map(function($nomina){
            $nomina->cargoUsuario = "Operador";
            return $nomina;
        });

        // unirlas todas (si esta repetida, se toma el valor de la ultima)
        $todas = $nominasOperador
            ->merge($nominasSupervisor)
            ->merge($nominasLider)
            ->sortBy('inventario.fechaProgramada');

        return (object)[
            'comoLider' => $nominasLider,
            'comoSupervisor' => $nominasSupervisor,
            'comoOperador' => $nominasOperador,
            'todas' => $todas
        ];
    }
    function experiencia(){
        // todo: la experiencia deberia contemplar solo las nominas que han sido terminadas
        // es decir, debe buscar en las nominasFinales/nominasDePago
        $nominas = $this->nominasComoTitular(null, null, null);
        return (object)[
            'comoLider' => count($nominas->comoLider),
            'comoSupervisor' => count($nominas->comoSupervisor),
            'comoOperador' => count($nominas->comoOperador),
            //'todas' => $nominas->todas
        ];
    }

    // Indica si el usuario, esta disponible para participar como lider en un inventario (no esta asignado a otro)
    function disponibleParaInventario($fecha, $turno){
        // un usuario (asumiendo que es lider), esta disponible cuando no este asignado a otra nomina
        // el mismo dia, y el mismo turno como lider, supervisor, o operador
        $nominas = $this->nominasComoTitular($fecha, $fecha, $turno);

        return  $nominas->comoLider->count()==0 &&
        $nominas->comoSupervisor->count()==0 &&
        $nominas->comoOperador->count()==0;
    }

    // #### Acciones
    //

    // ####  Getters
    function nombreCorto(){
        return "$this->nombre1 $this->apellidoPaterno";
    }
    function nombreCompleto(){
        return "$this->nombre1 $this->nombre2 $this->apellidoPaterno $this->apellidoMaterno";
    }
    function permisosAsignados(){
        // buscar cada uno de los permisos que tiene el usuario
        $perms = [];
        foreach ($this->roles as $role) {
            foreach ($role->perms as $perm){
                array_push($perms, $perm->name);
            }
        }
        return collect($perms)->unique()->toArray();
    }

    // ####  Setters
    //

    // #### Formatear respuestas
    // usado por Nominas::formatoPanelNomina
    static function formatoPanelNomina($user, $captador){
        if(!$user)
            return null;

        $experiencia = $user->experiencia();
        return [
            'id' => $user->id,
            'usuarioRUN' => $user->usuarioRUN,
            'usuarioDV' => $user->usuarioDV,
            'nombreCompleto' => $user->nombreCompleto(),
            'imagenPerfil' => $user->imagenPerfil,
            'experienciaComoLider' => $experiencia->comoLider,
            'experienciaComoSupervisor' => $experiencia->comoSupervisor,
            'experienciaComoOperador' => $experiencia->comoOperador,
            'captador' => $captador
        ];
    }

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