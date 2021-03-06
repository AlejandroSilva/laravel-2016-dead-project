<?php
namespace App\Http\Controllers;
use App;
use Auth;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Jobs\InformarNominaACliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
// Modelos
use App\Comunas;
use App\Inventarios;
use App\Locales;
use App\Nominas;
use App\Role;
use App\User;

use App\Services\NominaService;

class NominasController extends Controller {
//    public function __construct(/*\NominasService $nominasService*/){
//        parent::__construct();
//        //$this->nominasService = $nominasService;
//    }

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET nomina/{idNomina}
    function show_nomina($idNomina){
        $user = Auth::user();

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nómina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }
        // la nomina esta habilitada?
        if(!$nomina->habilitada){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nómina no habilitada',
                'descripcion' => 'La nomina que ha solicitado se encuentra deshabilitada. Es problable que el turno del inventario haya cambiado.'
            ]);
        }

        $verTodas = $user->can('nominas-verTodas');
        $editarTodas = $user->can('nominas-cambiarCualquierDotacion');
        $captadorPivot = $nomina->captadores()->find($user->id);
        $usuario_idCaptador = $captadorPivot? $captadorPivot->pivot->idCaptador : null;
        // el usuario tiene los permisos para ver la nominas? (es uno de los captadores asignados, o tiene permisos para ver todo)
        if(!$usuario_idCaptador && !$verTodas)
            return view('errors.403');

        return view('nominas.index-nomina', [
            'inventario' => $nomina->inventario,        // utilizado en el header
            'nomina' => Nominas::formatoPanelNomina($nomina),
            'comunas' => Comunas::all(),
            'permisos' => [
                // todo: agregar al permiso, el checkeo si la nomina esta completa?
                // para poder enviar debe tener los permisos, O ser el captador asociado (ambos no son necesarios)
                'cambiarLiderSupervisor' => $user->can('inventarios-cambiarLiderSupervisorCaptador'),
                'verTodasNominas' =>  $verTodas,
                'editarTodasNominas' =>  $editarTodas,
                'editarIdCaptador' =>  $usuario_idCaptador,

                'enviar' => $user->can('nominas-enviar'),
                'aprobar' => $user->can('nominas-aprobar'),
                'informar' => $user->can('nominas-informar'),
                'rectificar' => $user->can('nominas-rectificar')
            ]
        ]);
    }

    // GET programacionIG/nomina/{idNomina}/pdf-preview
    // Esta ruta es publica, se utiliza para generar los PDFs
    function show_nomina_pdfPreview($idNomina){
        // Todo validar permisos, con algo como token, request del mismo dominio, etc...
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

    // GET nominas/captadores
    function show_captadores(){
        // Todo: revisar los permisos
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador? $rolCaptador->users : [];

        return view('nominas.captadores', [
            'captadores' => $captadores
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
            ->sortBy('inventario.fechaProgramada')
            ->map('\App\Nominas::formatearConInventario')
            ->toArray();
        return response()->json( array_values($nominas), 200);
    }

    // PUT api/nomina/{idNomina}  // Modificar antiguo, no entrega un formato compacto, se debe reescribir
    function api_actualizar($idNomina, Request $request){
        // identificar la nomina indicada
        $nomina = Nominas::find($idNomina);
        if($nomina){
            // Actualizar con los datos entregados
            // Dotacion de la Nomina
            //if(isset($request->dotacionAsignada))
            //    $nomina->dotacionAsignada = $request->dotacionAsignada;
            // En el Lider, Supervisor, Captador1 y Captador 2 si la selecciona es '', se agrega un valor null al registro
            // Lider
            
            // pendiente, falta registrar cuando se realice un cambio de lider, supervisor, o captador a una nomina
            // pendiente, falta registrar el cambio de Hr.Lider y Hr.Equipo

            if(isset($request->idLider))
                // desde el front end, el value de 'sin lider' es igual a ''
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
            //  Dotacion Total
            if(isset($request->dotacionTotal))
                $nomina->set_dotacionTotal($request->dotacionTotal);
                //$nomina->dotacionTotal = $request->dotacionTotal;
            //  Dotacion Operadores
            if(isset($request->dotacionOperadores))
                $nomina->set_dotacionOperadores($request->dotacionOperadores);
                //$nomina->dotacionOperadores = $request->dotacionOperadores;
            // Hora llegada Lider
            if(isset($request->horaPresentacionLider))
                $nomina->horaPresentacionLider = $request->horaPresentacionLider;
            // hora llegada Equipo
            if(isset($request->horaPresentacionEquipo))
                $nomina->horaPresentacionEquipo = $request->horaPresentacionEquipo;
            $nomina->save();
            // entregar la informacion completa del inventario al que pertenece esta nomina
            $inventarioPadre = $nomina->inventario1? $nomina->inventario1 : $nomina->inventario2;
            return response()->json(Inventarios::formato_programacionIGSemanal($inventarioPadre), 200);
        }else{
            return response()->json([], 404);
        }
    }

    // GET api/nomina/{idNomina}
    function api_getNomina($idNomina){
        $nomina = Nominas::find($idNomina);
        return $nomina?
            response()->json(Nominas::formatoPanelNomina($nomina))
            :
            response()->json([], 404);
    }

    // GET api/nomina/{idNomina}/lideres-disponibles
    function api_lideresDisponibles($idNomina){
        // solo pueden acceder o
        $user = Auth::user();
        if(!$user)
            return response()->json(['error'=>'No tiene permisos.'], 403);

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Para asignar el Lider, la nómina debe estar Pendiente'], 400);

        return response()->json( $nomina->lideresDisponibles() );
    }

    // POST api/nomina/{idNomina}/lider/{usuarioRUN}
    function api_agregarLider(NominaService $nominaService, $idNomina, $usuarioRUN){
        // Revisar que el usuario tenga los permisos para cambiar el lider
        $user = Auth::user();
        $lider = User::where('usuarioRUN', $usuarioRUN)->first();
        $nomina = Nominas::find($idNomina);

        $res = $nominaService->agregarLider($user, $nomina, $lider);
        if(isset($res->error))
            return response()->json($res->error, 400);
        else
            return response()->json(Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200);  // nomina actualizada
    }
    // DELETE api/nomina/{idNomina}/lider
    function api_quitarLider(NominaService $nominaService, $idNomina){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);

        // quitar el lider de la nomina
        $res = $nominaService->quitarLider($user, $nomina);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json(Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200);  // nomina actualizada
    }
    // POST api/nomina/{idNomina}/supervisor/{usuarioRUN}
    function api_agregarSupervisor(NominaService $nominaService, $idNomina, $usuarioRUN){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        $supervisor = User::where('usuarioRUN', $usuarioRUN)->first();

        // seleccionar usuario como supervisor, solo si este existe
        $res = $nominaService->agregarSupervisor($user, $nomina, $supervisor);
        return isset($res->error)?
            response()->json($res->error, 400)
            :
            response()->json(Nominas::formatoPanelNomina( Nominas::find($idNomina) ));  // nomina actualizada
    }
    // DELETE api/nomina/{idNomina}/supervisor
    function api_quitarSupervisor(NominaService $nominaService, $idNomina){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);

        $res = $nominaService->quitarSupervisor($user, $nomina);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json( Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ) ); // entregar nomina actualizada
    }
    // POST nomina/{idNomina}/captador/{idUsuario}
    function api_agregarCaptador(NominaService $nominaService, $idNomina, $idUsuario){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        $captador = User::find($idUsuario);

        $res = $nominaService->agregarCaptador($user, $nomina, $captador);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json(Inventarios::formato_programacionIGSemanal($nomina->inventario), 200);
    }
    // DELETE 'nomina/{idNomina}/captador/{idUsuario}
    function api_quitarCaptador(NominaService $nominaService, $idNomina, $idUsuario){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        $captador = User::find($idUsuario);

        $res = $nominaService->quitarCaptador($user, $nomina, $captador);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json(Inventarios::formato_programacionIGSemanal($nomina->inventario), 200);
    }
    // PUT nomina/{idNomina}/captador/{idCaptador}
    function api_cambiarAsignadosDeCaptador(NominaService $nominaService, $idNomina, $idCaptador, Request $request){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        $captador = User::find($idCaptador);

        $res = $nominaService->cambiarAsignadosDeCaptador($user, $nomina, $captador, $request->asignados);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json(Inventarios::formato_programacionIGSemanal( Inventarios::find($nomina->inventario->idInventario) ));
    }

    // POST api/nomina/{idNomina}/operador/{usuarioRUN}
    function api_agregarOperador(NominaService $nominaService, $idNomina, $usuarioRUN, Request $request){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        // existe un operador con ese rut? (si se entrega un 204 y el frontend muestra un formulario de creacion)
        $operador = User::where('usuarioRUN', $usuarioRUN)->first();
        if(!$operador)
            return response()->json(['usuarioRUN'=>'No existe ningun usuario con ese RUN'], 204);

        // Agregar el operador a la nomina
        $res = $nominaService->agregarOperador($user, $nomina, $operador, $request->esTitular, $request->idCaptador);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json(Nominas::formatoPanelNomina( Nominas::find($idNomina) ), 200);  // se debe actualizar la nomina y dotacion
    }
    // DELETE api/nomina/{idNomina}/operador/{idOperador}
    function api_quitarOperador(NominaService $nominaService, $idNomina, $idOperador){
        $user = Auth::user();
        $nomina = Nominas::find($idNomina);
        $operador = User::find($idOperador);

        $res = $nominaService->quitarOperador($user, $nomina, $operador);
        return isset($res->error)?
            response()->json($res->error, $res->codigo)
            :
            response()->json( Nominas::formatoPanelNomina(Nominas::find($idNomina) ), 201);
    }

    function api_enviarNomina($idNomina){
        // el usuario esta logeado?
        $user = Auth::user();
        if(!$user)
            return response()->json(['error'=>'No tiene permisos para enviar la Nómina'], 403);

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);

        // tiene los permisos para hacer el cambio? (tiene los permisos O es el captador asignado)
        $esElCaptadorAsignado = $user->id==$nomina->idCaptador1 || $user->id==$nomina->idCaptador2;
        if(!$esElCaptadorAsignado && !$user->can('nominas-enviar'))
            return response()->json(['error'=>'No tiene permisos para enviar la Nómina'], 403);

        // la nomina esta pendiente?
        if($nomina->idEstadoNomina!=2)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Pendiente'], 400);

        // pasar al estado "Recibida"
        $nomina->idEstadoNomina = 3;
        $nomina->save();
        $nomina->addLog('Enviada a SEI', 'se envia nomina a SEI para su aprobación', 1, 0);

        return response()->json(
            Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_aprobarNomina($idNomina){
        // Puede aprobar la nomina solo si tiene los permisos
        $user = Auth::user();
        if(!$user || !$user->can('nominas-aprobar'))
            return response()->json(['error'=>'No tiene permisos para enviar la Nómina'], 403);

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
        $nomina->addLog('Aprobada por SEI', 'la nomina ha sido aprobada por SEI', 1, 0);

        return response()->json(
            Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_rechazarNomina($idNomina){
        // Puede rechazar la nomina solo si tiene los permisos
        $user = Auth::user();
        if(!$user || !$user->can('nominas-aprobar'))
            return response()->json(['error'=>'No tiene permisos para enviar la Nómina'], 403);

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);
        // la nomina esta Recibida o Aprobada?
        if($nomina->idEstadoNomina!=3 && $nomina->idEstadoNomina!=4)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Recibida o Aprobada'], 400);
        // volver al estado "Pendiente"
        $nomina->idEstadoNomina = 2;
        $nomina->save();
        $nomina->addLog('Nomina rechazada', 'la nomina ha sido rechazada por SEI', 1, 0);

        return response()->json(
            Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_informarNomina($idNomina, Request $request){
        Log::info("[NOMINA:INFORMAR_CLIENTE] enviando nomina idNomina:$idNomina ...");
        // Puede informar la nomina (y enviar el correo) solo si tiene los permisos
        $user = Auth::user();
        if(!$user || !$user->can('nominas-informar')){
            Log::info("[NOMINA:INFORMAR_CLIENTE:error] idNomina:$idNomina. No tiene permisos");
            return response()->json(['error'=>'No tiene permisos para enviar la Nómina'], 403);
        }

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            Log::info("[NOMINA:INFORMAR_CLIENTE:error] idNomina:$idNomina. Nomina no encontrada");
            return response()->json(['idNomina'=>'Nómina no encontrada'], 404);
        }

        // la nomina esta Aprobada?
        if($nomina->idEstadoNomina!=4){
            Log::info("[NOMINA:INFORMAR_CLIENTE:error] idNomina:$idNomina. Debe estar Aprobada");
            return response()->json(['idNomina'=>'La nómina debe estar en estado Aprobada'], 400);
        }

        // la nomina esta completa?
        if(!$nomina->tieneDotacionCompleta()){
            Log::info("[NOMINA:INFORMAR_CLIENTE:error] idNomina:$idNomina. Dotacion incompleta");
            return response()->json(['idNomina'=>'La nómina no esta completa'], 400);
        }

        // se puede omitir el cambio a informada, siempre que esta este siendo rectificada y se
        // tengan los permisos (que el usuario pueda rectificar)
        if(isset($request->omitirCorreo) && $request->omitirCorreo==true){
            // validar que tenga los permisos
            if(!$user->can('nominas-rectificar')){
                Log::info("[NOMINA:INFORMAR_CLIENTE:ERROR] idNomina:$idNomina. No tiene permisos para informar sin enviar correo");
                return response()->json(['error'=>'No tiene permisos para informar la nomina sin enviar el correo'], 403);
            }
            // todo valida si ha sido rectificada
            if($nomina->rectificada==false){
                Log::info("[NOMINA:INFORMAR_CLIENTE:ERROR] idNomina:$idNomina. No puede informar sin correo una nomina no rectificada");
                return response()->json(['error'=>'No puede informar sin enviar el correo en una nomina no rectificada'], 403);
            }

            $nomina->addLog('Informada (sin correo)', 'se marca como informada pero no se envia el correo a los clientes', 1, 0);
            Log::info("[NOMINA:INFORMAR_CLIENTE:OK] nomina idNomina:$idNomina marcada como enviada (pero no se envio el correo)");
        }else{
            // enviar correo
            dispatch(new InformarNominaACliente($nomina));
            $nomina->addLog('Informada a cliente', 'se notifica al cliente por correo de la nomina', 1, 0);
            Log::info("[NOMINA:INFORMAR_CLIENTE:OK] nomina idNomina:$idNomina enviada al cliente");
        }

        // pasasr al estado "informada"
        $nomina->idEstadoNomina = 5;
        $nomina->save();

        return response()->json(
            Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200
        );
    }
    function api_rectificarNomina($idNomina){
        // Puede informar la nomina (y enviar el correo) solo si tiene los permisos
        $user = Auth::user();
        if(!$user || !$user->can('nominas-rectificar'))
            return response()->json(['error'=>'No tiene permisos para rectificar la Nómina'], 403);

        // la nomina existe?
        $nomina = Nominas::find($idNomina);
        if(!$nomina)
            return response()->json(['idNomina'=>'Nomina no encontrada'], 404);

        // la nomina esta Informada?
        if($nomina->idEstadoNomina!=5)
            return response()->json(['idNomina'=>'La nomina debe estar en estado Informada'], 400);

        // al rectificar, se pasa al estado Pendiente, y se agrega el "flag" rectificada
        $nomina->idEstadoNomina = 2;
        $nomina->rectificada = true;
        $nomina->save();
        $nomina->addLog('Rectificar', 'La nomina ha sido rectificada', 1, 0);

        // ToDo: enviar los correos
        return response()->json(
            Nominas::formatoPanelNomina( Nominas::find($nomina->idNomina) ), 200
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
                    Log::info("[NOMINA:INFORMAR_DISPONIBLE:OK] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') informado correctamente.");
                }else{
                    Log::info("[NOMINA:INFORMAR_DISPONIBLE:ERROR] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (idInventario '$inventario->idInventario') ya habia sido informado.");
                }
                return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
            }else {
                // inventario con esa fecha no existe
                $errorMsg = "CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia'; no existe un inventario programado para el idLocal '$local->idLocal' en esa fecha.";
                Log::info("[NOMINA:INFORMAR_DISPONIBLE:ERROR] $errorMsg");
                return response()->json(['msg' => $errorMsg], 404);
            }
        } else{
            // local de ese usuario, con ese ceco no existe
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            Log::info("[NOMINA:INFORMAR_DISPONIBLE:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }
    }

    // POST nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-nomina-pago
    function api_informarNominaPago($idCliente, $ceco, $annoMesDia, Request $request){
        // El Local existe?
        $local = Locales::where('idCliente', '=', $idCliente)
            ->where('numero', '=', $ceco)
            ->first();
        if(!$local){
            $errorMsg = "no existe el CECO '$ceco' del idCliente '$idCliente'";
            Log::info("[NOMINA:INFORMAR_NOMINA_PAGO:ERROR] $errorMsg");
            return response()->json(['msg'=>$errorMsg], 404);
        }

        // Existe un inventario en esa fecha?
        $inventario = Inventarios::where('idLocal', '=', $local->idLocal)
            ->where('fechaProgramada', $annoMesDia)
            //->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            //->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->first();
        if(!$inventario){
            $errorMsg = "CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia'; no existe un inventario programado para el idLocal '$local->idLocal' en esa fecha.";
            Log::info("[NOMINA:INFORMAR_NOMINA_PAGO:ERROR] $errorMsg");
            return response()->json(['msg' => $errorMsg], 404);
        }

        // actualizar la URL de las nominas de pago de los locales
        $urlNomina = $request->urlNominaPago;
        $inventario->nominaDia->urlNominaPago = $urlNomina;
        $inventario->nominaDia->save();
        $inventario->nominaNoche->urlNominaPago = $urlNomina;
        $inventario->nominaNoche->save();
        Log::info("[NOMINA:INFORMAR_NOMINA_PAGO:OK] CECO '$ceco', idCliente '$idCliente', dia '$annoMesDia' (urlNomina: $urlNomina)");
        return response()->json(Inventarios::with(['nominaDia', 'nominaNoche'])->find($inventario->idInventario), 200);
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
        // buscar la nomina (si existe)
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }

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
    }

    // GET programacionIG/nomina/{publicIdNomina}/excel           RUTA PUBLICA
    function show_nomina_excelDownload($publicIdNomina) {
        // intentar de des-encriptar el id de usuario
        try {
            $idNomina = Crypt::decrypt($publicIdNomina);
        } catch (DecryptException $e) {
            return view('errors.errorConMensaje', ['titulo' => 'Link de descarga invalido', 'descripcion' => 'El link de descarga que ha ocupado es invalido, actualice la página desde donde lo obtuvo, o contacte al departamento de informática de SEI.']);
        }
        // buscar la nomina (si existe)
        $nomina = Nominas::find($idNomina);
        if(!$nomina){
            return view('errors.errorConMensaje', [
                'titulo' => 'Nomina no encontrada',
                'descripcion' => 'La nomina que ha solicitado no ha sido encontrada. Verifique que el identificador sea el correcto y que el inventario no haya sido eliminado.'
            ]);
        }

        $inventario = $nomina->inventario;
        $local = $inventario->local;
        // crear el archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();

        // Datos de Inventario y de Local
        $sheet->fromArray([
            ['Datos de Inventario:'],
            ['Cliente', $local->cliente->nombreCorto, 'Dotación Operadores', $nomina->dotacionOperadores],
            ['Local', "($local->numero) $local->nombre", 'Dotación Total', $nomina->dotacionTotal],
            ['Fecha Programada', $inventario->fechaProgramadaF(), ],
            ['Hr. llegada Líder', $nomina->horaPresentacionLiderF()],
            ['Hr. llegada Equipo', $nomina->horaPresentacionEquipoF()],
            [],
            ['Datos de Local'],
            ['Dirección', $local->direccion->direccion, 'Hr. Apertura', $local->horaAperturaF()],
            ['Comuna', $local->direccion->comuna->nombre, 'Hr. Cierre', $local->horaCierreF()],
            ['Región', $local->direccion->comuna->provincia->region->numero, 'Teléfono 1', $local->telefono1],
            ['Formato Local', $local->formatoLocal->nombre, 'Teléfono 2', $local->telefono2],
            ['', '', 'Correo', $local->emailContacto ]

        ], NULL, 'A1');

        // Dotación completa de la nomina
        $nominaCompleta = [
            ['Nomina:'],
            ['Código', 'Nombre', 'RUN', 'Cargo']
        ];
        // Agregar Lider
        $lider = $nomina->lider;
        if(isset($lider))
            array_push($nominaCompleta, [
                $lider->usuarioRUN, $lider->nombreCompleto(), $lider->usuarioRUN."-".$lider->usuarioDV, 'Líder'
            ]);
        // Agregar Supervisor
        $super = $nomina->supervisor;
        if(isset($super))
            array_push($nominaCompleta, [
                $super->usuarioRUN, $super->nombreCompleto(), $super->usuarioRUN."-".$super->usuarioDV, 'Supervisor'
            ]);
        // Dotación Titular
        $dTitular = $nomina->dotacionTitular;
        for($i=0; $i<sizeof($dTitular); $i++){
            $op = $dTitular[$i];
            array_push($nominaCompleta, [
                $op->usuarioRUN, $op->nombreCompleto(), $op->usuarioRUN."-".$op->usuarioDV, 'Operador'
            ]);
        }
        // Dotación Reemplazo
        $dReemplazo = $nomina->dotacionReemplazo;
        for($i=0; $i<sizeof($dReemplazo); $i++){
            $op = $dReemplazo[$i];
            array_push($nominaCompleta, [
                $op->usuarioRUN, $op->nombreCompleto(), $op->usuarioRUN."-".$op->usuarioDV, 'Operador Reemplazo'
            ]);
        }
        //
        $sheet->fromArray($nominaCompleta, NULL, 'A16');

        // Aplicar estilos al documento
        // Titulos en negrita
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('B2')->getFont()->setBold(true);
        $sheet->getStyle('B3')->getFont()->setBold(true);
        $sheet->getStyle('B4')->getFont()->setBold(true);
        $sheet->getStyle('A8')->getFont()->setBold(true);
        $sheet->getStyle('A16')->getFont()->setBold(true);
        // las columnas deben tener un ancho "dinamico"
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        // las celdas alineadas a la izquierda
        $sheet->getStyle("A")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("C")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("D")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // guardar y descargar el archivo
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = public_path()."/tmp/nomina_".$local->cliente->nombreCorto."-".$local->numero."_".md5(uniqid(rand(), true)).".xlsx";
        $downloadFileName = "nomina ".$local->cliente->nombreCorto." ".$local->numero." ".$inventario->fechaProgramada.".xlsx";
        $excelWritter->save($randomFileName);
        return response()->download($randomFileName, $downloadFileName);
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
        // ordenar en el metodo controlador que lo llame
        return $query->get();
    }
}
