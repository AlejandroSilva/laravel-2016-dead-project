<?php
namespace App\Http\Controllers;
use App;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Jobs\InformarNominaACliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use Knp\Snappy\Pdf;
// Modelos
use App\Comunas;
use App\Inventarios;
use App\Locales;
use App\Nominas;
use App\Role;
use App\User;
class NominasController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET programacionIG/nomina/{idNomina}
    function show_nomina($idNomina){
        // Todo: agregar seguriddad, solo para usuarios con permisos
//        $user = Auth::user();
//        if(!$user || !$user->can('programaAuditorias_ver'))
//            return view('errors.403');
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }
        return view('operacional.nominas.nomina', [
            'nomina' => Nominas::formatearConLiderSupervisorCaptadorDotacion($nomina),
            'inventario' => Inventarios::formatoClienteFormatoRegion($nomina->inventario),
            'comunas' => Comunas::all()
        ]);
    }
    // GET programacionIG/nomina/{idNomina}/pdf-preview
    function show_nomina_pdfPreview($idNomina){
        // Todo validar permisos, token, etc...
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }
        return view('pdfs.nominaIG', [
            'nomina' => $nomina,
            'inventario' => $nomina->inventario,
            'lider' => $nomina->lider,
            'supervisor' => $nomina->supervisor,
            'dotacionTitular' => $nomina->dotacionTitular,
            'dotacionReemplazo' => $nomina->dotacionReemplazo,
        ]);
    }
    // GET programacionIG/nomina/{publicIdNomina}/pdf           RUTA PUBLICA
    function show_nomina_pdfDownload($publicIdNomina){
        // intentar de des-encriptar el id de usuario
        try {
            $idNomina = Crypt::decrypt($publicIdNomina);
        } catch (DecryptException $e) {
            return view('errors.errorConMensaje', [
                'titulo' => 'Link de descarga invalido',
                'descripcion' => 'El link de descarga que ha ocupado es invalido, actualice la página desde donde lo obtuvo, o contacte al departamento de informática de SEI.'
            ]);
        }
        $nomina = Nominas::find($idNomina);
        if($nomina){
            // nombre del archivo
            $inventario = $nomina->inventario;
            $cliente = $inventario->local->cliente->nombreCorto;
            $ceco = $inventario->local->numero;
            $fechaProgramada = $inventario->fechaProgramada;
            $fileName = "nomina $cliente $ceco $fechaProgramada.pdf";
            if(App::environment('production')) {
                return \PDF::loadFile("http://sig.seiconsultores.cl/programacionIG/nomina/$idNomina/pdf-preview")
                    ->download($fileName);
            }else{
                // stream, download
                return \PDF::loadFile("http://localhost/programacionIG/nomina/$idNomina/pdf-preview")
                    ->download($fileName);  //->stream('nomina.pdf');
            }
        }else{
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }
    }

    // GET nominas/captadores
    function show_captadores(){
        // Todo: revisar los permisos
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador? $rolCaptador->users : [];
        
        return view('nominas.captadores', [
            'captadores' => $captadores
        ]);
    }
    // GET nominas/captador/{idCaptador}
    function show_nominasCaptador($idCaptador){
        // todo revisar los permisos
        $usuario = User::find($idCaptador);
        if(!$usuario){
            return view('errors.errorConMensaje', [
                'titulo' => 'Captador no encontrado', 'descripcion' => 'El captador que busca no ha sido encontrado.'
            ]);
        }

        // buscar los "proximos" inventarios del captador
        $nominas = $this->buscar( (object)[
            'fechaInicio' => \Carbon\Carbon::now()->format("Y-m-d"),
            'idCaptador1' => $idCaptador
        ])
            ->map('\App\Nominas::formatearConInventario');

        return view('nominas.captador', [
            'nominas' => $nominas
        ]);
    }
    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
    // GET api/nominas/buscar
    function api_buscar(Request $request){
        // todo validar permisos y todo eso

        $nominas = $this->buscar( (object)[
            'fechaInicio' => $request->query('fechaInicio'),
            'fechaFin' => $request->query('fechaFin'),
            'idCaptador1' => $request->query('idCaptador1')
        ])
            ->map('\App\Nominas::formatearConInventario');
        return response()->json($nominas, 200);
    }

    // PUT api/nomina/{idNomina}  // Modificar antuguo, no entrega un formato compacto, se debe reescribir
    function api_actualizar($idNomina, Request $request){
        // identificar la nomina indicada
        $nomina = Nominas::find($idNomina);
        if($nomina){
            // Actualizar con los datos entregados
            // Dotacion de la Nomina
            if(isset($request->dotacionAsignada))
                $nomina->dotacionAsignada = $request->dotacionAsignada;
            // En el Lider, Supervisor, Captador1 y Captador 2 si la selecciona es '', se agrega un valor null al registro
            // Lider
            if(isset($request->idLider))
                $nomina->idLider = $request->idLider==''? null : $request->idLider;
            // Supervisor
            if(isset($request->idSupervisor))
                $nomina->idSupervisor = $request->idSupervisor==''? null : $request->idSupervisor;
            // Captador 1
            if(isset($request->idCaptador1))
                $nomina->idCaptador1 = $request->idCaptador1==''? null : $request->idCaptador1;
            // Captador 2
            if(isset($request->idCaptador2))
                $nomina->idCaptador2 = $request->idCaptador2==''? null : $request->idCaptador2;
            //  Dotacion Captador 1
            if(isset($request->dotacionCaptador1))
                $nomina->dotacionCaptador1 = $request->dotacionCaptador1;
            //  Dotacion Captador 2
            if(isset($request->dotacionCaptador2))
                $nomina->dotacionCaptador2 = $request->dotacionCaptador2;
            // Hora llegada Lider
            if(isset($request->horaPresentacionLider))
                $nomina->horaPresentacionLider = $request->horaPresentacionLider;
            // hora llegada Equipo
            if(isset($request->horaPresentacionEquipo))
                $nomina->horaPresentacionEquipo = $request->horaPresentacionEquipo;
            $nomina->save();
            // entregar la informacion completa del inventario al que pertenece esta nomina
            $inventarioPadre = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
            return response()->json(
                Inventarios::with([
                    'local.cliente',
                    'local.formatoLocal',
                    'local.direccion.comuna.provincia.region',
                    'nominaDia',
                    'nominaNoche',
                    'nominaDia.lider',
                    'nominaNoche.lider',
                    'nominaDia.captador',
                    'nominaNoche.captador',
                ])->find($inventarioPadre->idInventario),
                200
            );
        }else{
            return response()->json([], 404);
        }
    }

    // -- cambios en la dotacion de las nominas
    // GET api/nomina/{idNomina}/dotacion
    function api_get($idNomina){
        $nomina = Nominas::find($idNomina);
        return $nomina?
            response()->json(Nominas::formatearConLiderSupervisorCaptadorDotacion($nomina))
            :
            response()->json([], 404);
    }
    // POST api/nomina/{idNomina}/lider/{usuarioRUN}
    function api_agregarLider($idNomina, $usuarioRUN){
        // Todo: El usuario tiene los permisos para agregar un lider a una nomina?
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para asignar el Lider, la nómina debe estar Pendiente'], 400);
        // el usuario existe? es un lider?
        $usuario = User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$usuario)
            return response()->json(['usuarioRUN'=>'Usuario no encontrado'], 404);
        if(!$usuario->hasRole('Lider'))
            return response()->json(['usuarioRUN'=>'El usuario no es un Lider'], 400);
        // se agrega el lider y se actualiza la dotacion
        $nomina->idLider = $usuario->id;
        $nomina->save();
        // entregar nomina actualizada
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 201
        );
    }
    // DELETE api/nomina/{idNomina}/lider
    function api_quitarLider($idNomina){
        // ToDo: revisar los permisos, y ver que no haya sido enviado
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para quitar el Lider, la nómina debe estar Pendiente'], 400);
        // quitar el lider
        $nomina->idLider = null;
        $nomina->save();
        // entregar nomina actualizada
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 201
        );
    }
    // POST api/nomina/{idNomina}/supervisor/{usuarioRUN}
    function api_agregarSupervisor($idNomina, $usuarioRUN){
        // Todo: El usuario tiene los permisos para agregar un supervisor a una nomina?
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para quitar el supervisor, la nómina debe estar Pendiente'], 400);
        // el usuario existe? es un lider?
        $usuario = User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$usuario)
            return response()->json(['usuarioRUN'=>'Usuario no encontrado'], 404);
        if(!$usuario->hasRole('Supervisor'))
            return response()->json(['usuarioRUN'=>'El usuario no es un Supervisor'], 400);

        // se agrega el lider y se actualiza la dotacion
        $nomina->idSupervisor = $usuario->id;
        $nomina->save();
        // entregar nomina actualizada
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 201
        );
    }
    // DELETE api/nomina/{idNomina}/supervisor
    function api_quitarSupervisor($idNomina){
        // ToDo: revisar los permisos, y ver que no haya sido enviado
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para quitar el supervisor, la nómina debe estar Pendiente'], 400);
        // quitar el lider
        $nomina->idSupervisor = null;
        $nomina->save();
        // entregar nomina actualizada
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    // POST api/nomina/{idNomina}/operador/{usuarioRUN}
    function api_agregarOperador($idNomina, $usuarioRUN, Request $request){
        // Todo: El usuario tiene los permisos para agregar un usuario a una nomina?
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json('Nomina no encontrada', 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para agregar el usuario, la nómina debe estar Pendiente'], 400);
        // el operador existe? se entrega un 204 y en el frontend se muestra un formulario
        $operador = User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$operador)
            return response()->json('', 204);
        // Si el operador ya esta en la nomina, no hacer nada y devolver la lista como esta
        $operadorExiste = $nomina->usuarioEnDotacion($operador);
        if($operadorExiste)
            return response()->json(
                Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200);
        // Todo: trabajar este dato
        // Si la dotacion esta completa, no hacer nada y retornar el error
        if(sizeof($nomina->dotacion) >= $nomina->dotacionAsignada)
            return response()->json('Ha alcanzado el maximo de dotacion', 400);
        if($request->esTitular==true){
            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, ['titular'=>true]);
        }else{
            // No hay problemas en este punto, agregar usuario y retornar la dotacion
            $nomina->dotacion()->save($operador, ['titular'=>false]);
        }
        // se debe actualizar la dotacion
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 201
        );
    }
    // DELETE api/nomina/{idNomina}/operador/{usuarioRUN}
    function api_quitarOperador($idNomina, $usuarioRUN){
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json('Nomina no encontrada', 404);
        // la nomina se encuentra pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'Para quitar el usuario, la nómina debe estar Pendiente'], 400);
        // el operador existe?
        $usuario = User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$usuario)
            return response()->json('Operador no encontrado', 404);
        $nomina->dotacion()->detach($usuario);
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 201
        );
    }
    function api_enviarNomina($idNomina){
        // Todo: revisar si tiene los permisos
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Pendiente'], 400);
        // pasar al estado "Recibida"
        $nomina->idEstadoNomina = 3;
        $nomina->save();
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_aprobarNomina($idNomina){
        // Todo: revisar si tiene los permisos
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta Recibida?
        if($nomina->idEstadoNomina!=3)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Recibida'], 400);
        // pasasr al estado "Aprobada"
        $nomina->idEstadoNomina = 4;
        $nomina->save();
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_rechazarNomina($idNomina){
        // Todo: revisar si tiene los permisos
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta Recibida?
        if($nomina->idEstadoNomina!=3)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Recibida'], 400);
        // volver al estado "Pendiente"
        $nomina->idEstadoNomina = 2;
        $nomina->save();
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_informarNomina($idNomina){
        // Todo: revisar si tiene los permisos
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta Aprobada?
        if($nomina->idEstadoNomina!=4)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Aprobada'], 400);
        // pasasr al estado "informada"
        $nomina->idEstadoNomina = 5;
        $nomina->save();

        // ToDo: enviar los correos
        dispatch(new InformarNominaACliente($nomina));

        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_rectificarNomina($idNomina){
        // Todo: revisar si tiene los permisos
        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta Informada?
        if($nomina->idEstadoNomina!=5)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Informada'], 400);
        // al rectificar, se pasa al estado Pendiente
        $nomina->idEstadoNomina = 2;
        $nomina->save();
        // ToDo: enviar los correos
        return response()->json(
            Nominas::formatearConLiderSupervisorCaptadorDotacion( Nominas::find($nomina->idNomina) ), 200
        );
    }
    /**
     * ##########################################################
     * API DE INTERACCION CON LA OTRA PLATAFORMA
     * ##########################################################
     */
    // POST api/nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible
    function api_informarDisponible($idCliente, $ceco, $annoMesDia, Request $request){
//        $fecha = explode('-', $annoMesDia);
//        $anno = $fecha[0];
//        $mes  = $fecha[1];
        // Buscar el Local (por idCliente y CECO)
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if($local) {
            // Buscar inventario
            $inventario = Inventarios::where('idLocal', '=', $local->idLocal)
                ->where('fechaProgramada', $annoMesDia)
                //->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
                //->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
                ->first();
            if($inventario) {
                // fijar la 'fechaSubidaNomina'
                $nominaDia = $inventario->nominaDia;
                $nominaNoche = $inventario->nominaNoche;
                // Si la fecha de subida ya habia sido fijada, no cambiar esa fecha
                // esto puede suceder cuando re-suben la nomina para corregir algun error
                if($nominaDia->fechaSubidaNomina=='0000-00-00'){
                    $nominaDia->fechaSubidaNomina = Carbon::now();
                    $nominaNoche->fechaSubidaNomina = Carbon::now();
                    $nominaDia->save();
                    $nominaNoche->save();
                    Log::info("[NOMINA:INFORMAR_REALIZADO:OK] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') informado correctamente.");
                }else{
                    Log::info("[NOMINA:INFORMAR_REALIZADO:ERROR] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') ya habia sido informado.");
                }
                return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
            }else {
                // inventario con esa fecha no existe
                $errorMsg = "CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia'; no existe un inventario programado para el idLocal '$local->idLocal' en esa fecha.";
                Log::info("[NOMINA:INFORMAR_REALIZADO:ERROR] $errorMsg");
                return response()->json(['msg' => $errorMsg], 404);
            }
        } else{
            // local de ese usuario, con ese ceco no existe
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            Log::info("[NOMINA:INFORMAR_DISPONIBLE:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }
        return response()->json(['msg'=>'falta por implementar'], 404);
    }


    /* ***************************************************/

    function buscar($peticion){
        $query = Nominas::with([]);
        $query->where('habilitada', true);

        // Buscar por idCaptador1
        if(isset($peticion->idCaptador1)){
            $idCaptador1 = $peticion->idCaptador1;
            $query->where('idCaptador1', $idCaptador1);
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

//        $query->orderBy('inventario.fechaProgramada');
        return $query->get();
    }
}
