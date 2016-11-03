<?php namespace App\Services;

use App\Contracts\NominaServiceContract;

class NominaService implements NominaServiceContract {
    // Captador
    public function agregarCaptador($user, $nomina, $captador){
        // TODO #### VALIDAR PERMISOS DE USUARIO
//        if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
//            return $this->_error('auth', 'No tiene permisos para cambiar un Lider.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        //if(!$nomina->estaDisponible())
        //    return $this->_error('idNomina', 'Para cambiar un captador, la nómina debe estar Pendiente', 400);

        // #### VALIDAR CAPTADOR
        if(!$captador)
            return $this->_error('captador', 'El usuario indicado no existe', 400);
        // el usuario indicado, es un lider?
        if(!$captador->hasRole('Captador'))
            return $this->_error('captador', 'El usuario indicado no es un Líder', 400);
        // revisar que no haya sido asignado anteriormente
        $captadorAgregadoPreviamente = $nomina->captadores()->find($captador->id);
        if($captadorAgregadoPreviamente)
            return $this->_error('captador', 'El captador ya esta asignado a la nomina', 400);

        // finalmente agregar el captador, por defecto se deja asignado 0 operadores
        $nomina->agregarCaptador($captador, 0);

        $nomina->addLog("Asignacion de Captador", "Se asigna a ".$captador->nombreCorto()." ($captador->id) como Captador de nomina.");
        return [];
    }
    public function quitarCaptador($user, $nomina, $captador){
        // TODO #### VALIDAR PERMISOS DE USUARIO
        //if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
        //    return $this->_error('auth', 'No tiene permisos para cambiar un Lider.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        //if(!$nomina->estaDisponible())
        //    return $this->_error('idNomina', 'Para quitar al Líder, la nómina debe estar Pendiente', 400);

        // #### VALIDAR CAPTADOR
        // el captador existe?
        if(!$captador)
            return $this->_error('captador', 'El usuario indicado no existe', 400);
        // el captador esta asignado?
        $captadorAgregadoPreviamente = $nomina->captadores()->find($captador->id);
        if(!$captadorAgregadoPreviamente)
            return $this->_error('captador', 'El captador no ha sido asignado a la nomina', 400);
        // no esta permitido quitar el captador SEI
        if($captador->id == 1)
            return $this->_error('captador', 'No esta permitido quitar el Captador SEI', 400);

        // finalmente quitar el captador de la nomina
        $nomina->captadores()->detach($captador->id);
        $nomina->addLog("Asignacion de Captador", "Se quita a ".$captador->nombreCorto()." ($captador->id) de los Captadores de nomina.");
        return [];
    }
    public function cambiarAsignadosDeCaptador($user, $nomina, $captador, $asignados){
        // TODO #### VALIDAR PERMISOS DE USUARIO

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // #### VALIDAR CAPTADOR
        // captador existe?
        if(!$captador)
            return response()->json(['idUsuario'=>'El usuario no existe'], 400);
        // el captador esta asignado?
        $captadorAgregadoPreviamente = $nomina->captadores()->find($captador->id);
        if(!$captadorAgregadoPreviamente)
            return $this->_error('captador', 'El captador no ha sido asignado a la nomina', 400);

        // actualizar la cantidad de operadores
        $captadorAgregadoPreviamente->pivot->operadoresAsignados  = $asignados;
        $captadorAgregadoPreviamente->pivot->save();
        return [];
    }

    // Lider
    public function agregarLider($user, $nomina, $lider){
        // #### VALIDAR PERMISOS DE USUARIO
        if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
            return $this->_error('auth', 'No tiene permisos para cambiar un Lider.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para agregar al Líder, la nómina debe estar Pendiente', 400);

        // #### VALIDAR LIDER
        if(!$lider)
            return $this->_error('lider', 'El usuario indicado no existe', 400);
        // el usuario indicado, es un lider?
        if(!$lider->hasRole('Lider'))
            return $this->_error('lider', 'El usuario indicado no es un Líder', 400);

        // seleccionar usuario como lider, solo si este existe
        $nomina->idLider = $lider->id;
        $nomina->save();
        $nomina->addLog("Cambio de Lider", "Se asigna a ".$lider->nombreCorto()." ($lider->id) como Lider de nomina.");
        return [];
    }
    public function quitarLider($user, $nomina){
        // #### VALIDAR PERMISOS DE USUARIO
        if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
            return $this->_error('auth', 'No tiene permisos para cambiar un Lider.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para quitar al Líder, la nómina debe estar Pendiente', 400);

        // #### VALIDAR LIDER
        $nomina->idLider = null;
        $nomina->save();
        $nomina->addLog("Cambio de Lider", "Se quita el lider actual de la nomina.");
        return [];
    }

    // Supervisor
    public function agregarSupervisor($user, $nomina, $supervisor){
        // #### VALIDAR PERMISOS DE USUARIO
        if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
            return $this->_error('auth', 'No tiene permisos para cambiar el Supervisor.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para agregar al Líder, la nómina debe estar Pendiente', 400);

        // #### VALIDAR Supervisor
        // el usuario existe?
        if(!$supervisor)
            return $this->_error('supervisor', 'El usuario indicado no existe', 400);
        // el usuario indicado, es un lider?
        if(!$supervisor->hasRole('Supervisor'))
            return $this->_error('supervisor', 'El usuario indicado no es un Supervisor', 400);

        // seleccionar usuario como lider, solo si este existe
        $nomina->idSupervisor = $supervisor->id;
        $nomina->save();
        $nomina->addLog("Cambio de Supervisor", "Se asigna a ".$supervisor->nombreCorto()." ($supervisor->id) como Supervisor de la nomina.");
        return [];
    }
    public function quitarSupervisor($user, $nomina){
        // #### VALIDAR PERMISOS DE USUARIO
        if(!$user || !$user->can('inventarios-cambiarLiderSupervisorCaptador'))
            return $this->_error('auth', 'No tiene permisos para cambiar el Supervisor.', 403);

        // #### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para quitar al Supervisor, la nómina debe estar Pendiente', 400);

        // #### VALIDAR LIDER
        $nomina->idSupervisor = null;
        $nomina->save();
        $nomina->addLog("Cambio de Supervisor", "Se quita al supervisor actual de la nomina.");
        return [];
    }

    // Operadores
    public function agregarOperador($user, $nomina, $operador, $titular, $idCaptadorAsignado){
        // el usuario esta logeado?
        if(!$user)
            return $this->_error('auth', 'Necesitas estar logeado', 401);

        // ### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para agregar al operador, la nómina debe estar Pendiente', 400);

        // #### VALIDAR PERMISOS DE USUARIO
        $esCaptadorSEI = $user->can('nominas-cambiarCualquierDotacion');
        $esCaptadorDeLaDotacion = $idCaptadorAsignado==$user->id;

        // captadorDestino es valido? (el captadorDetino esta asignado como captador para esta nomina)
        if(!$nomina->captadores->find($idCaptadorAsignado))
            return $this->_error('idCaptador', 'Esta asignando un operador a un captador que no se encuentra en la nomina', 400);

        // el usuario puede modificar la nomina de "captador SEI"? (solo el "captador SEI" puede)
        if($idCaptadorAsignado==1 && !$esCaptadorSEI)
            return $this->_error('auth', 'No tiene permisos para cambiar la dotacion SEI', 403);

        // el usuario puede modificar la dotacion? (es el captador indicado, o es el "captador SEI"?
        if(!$esCaptadorDeLaDotacion && !$esCaptadorSEI)
            return $this->_error('auth', 'No tiene permisos para cambiar esta dotacion', 403);

        // VALIDAR OPERADOR
        // el operador esta bloqueado de participar?
        if( $operador->bloqueado==true )
            return $this->_error('usuarioRUN', 'El usuario esta bloqueado, no puede participar de inventarios', 400);

        // Si el operador ya esta en la nomina, no hacer nada y devolver la lista como esta
        $operadorExiste = $nomina->usuarioEnDotacion($operador->id);
        if($operadorExiste)
            return $this->_error('idOperador', 'El operador ya se ha agregado', 400);
        if($operador->id==$nomina->idLider)
            return $this->_error('idOperador', 'El operador ya se encuentra agregado como lider', 400);
        if($operador->id==$nomina->idSupervisor)
            return $this->_error('idOperador', 'El operador ya se encuentra agregado como supervisor', 400);

        // AGREGAR
        // Si es titular, ver si la dotacion esta completa
        if($titular==true){
            if($nomina->tieneDotacionCompleta())
                return $this->_error('idNomina', 'Ha alcanzado el maximo de dotacion', 400);

            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, [
                'titular'       => true,
                'idRoleAsignado'=> 17,
                'idCaptador'    => $idCaptadorAsignado
            ]);
            return [];
        }else{
            // No existe restriccion a cuantos operadores de reemplazo pueden haber
            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, [
                'titular'       => false,
                'idRoleAsignado'=> 17,
                'idCaptador'    => $idCaptadorAsignado
            ]);
            return [];
        }
    }
    public function quitarOperador($user, $nomina, $operador){
        // el usuario esta logeado?
        if(!$user)
            return $this->_error('auth', 'Necesitas estar logeado', 401);

        // ### VALIDAR NOMINA
        // la nomina existe?
        if(!$nomina)
            return $this->_error('idNomina', 'Nomina no encontrada', 404);

        // la nomina esta pendiente?
        if(!$nomina->estaDisponible())
            return $this->_error('idNomina', 'Para agregar al operador, la nómina debe estar Pendiente', 400);

        // #### VALIDAR OPERADOR
        // usuario existe?
        if(!$operador)
            return $this->_error('idOperador', 'Usuario no encontrado', 404);

        // Si el operador ya esta en la nomina, no hacer nada y devolver la lista como esta
        $operadorPivot = $nomina->usuarioEnDotacion($operador->id);
        if(!$operadorPivot)
            return $this->_error('idOperador', 'El operador no se encuentra en la nomina', 400);

        // #### VALIDAR PERMISOS DE USUARIO
        $idCaptadorAsignado = $operadorPivot->pivot->idCaptador;
        $esCaptadorSEI = $user->can('nominas-cambiarCualquierDotacion');
        $esCaptadorDeLaDotacion = $user->id==$idCaptadorAsignado;

        // el usuario puede modificar la nomina de "captador SEI"? (solo el "captador SEI" puede)
        if($idCaptadorAsignado==1 && !$esCaptadorSEI)
            return $this->_error('auth', 'No tiene permisos para cambiar la dotacion SEI', 403);

        // el usuario puede modificar la dotacion? (es el captador indicado, o es el "captador SEI"?
        if(!$esCaptadorDeLaDotacion && !$esCaptadorSEI)
            return $this->_error('auth', 'No tiene permisos para cambiar esta dotacion', 403);

        // En este punto se pasaron todas las validaciones...
        $nomina->dotacion()->detach($operadorPivot);

        return [];
    }

    // privados
    private function _error($field, $msg, $codigo){
        return (object)[
            'error'=>[
                "$field"=>$msg
            ],
            'codigo'=>$codigo
        ];
    }
}

#en la mayoria de las nominas, no se ve el personal, ni la dotacion sei, esto pasa porque el
#captador sei no esta asignado a todas las nominas (por defecto esta bernardita)