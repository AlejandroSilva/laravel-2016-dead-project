<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Redirect;
use Log;
// Carbon
use Carbon\Carbon;
// Crypt
use Crypt;
// Modelos
use App\Clientes;
use App\Inventarios;
use App\Locales;
use App\Nominas;
// Auth
use App\Role;
use Auth;

class InventariosController extends Controller {

     // * ##########################################################
     // *               RUTAS QUE GENERAN VISTAS
     // * ##########################################################

    // GET inventarios/programacion-mensual
    public function show_programacionMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('inventarios-verProgramacion'))
            return view('errors.403');
        
        return view('inventarios.programacion-mensual', [
            'puedeAgregarInventarios'   => $user->can('inventarios-crearModificarEliminar')? "true":"false",
            'puedeModificarInventarios' => $user->can('inventarios-crearModificarEliminar')? "true":"false",
            'clientes' => Clientes::todos_conLocales(),
        ]);
    }

    // GET inventarios/programacion-semanal
    public function show_programacionSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('inventarios-verProgramacion'))
            return view('errors.403');

        // Clientes
        $clientes  = Clientes::all();
        // Captadores
        $rolCaptador = Role::where('name', 'Captador')->first();
        $captadores = $rolCaptador!=null? $rolCaptador->users : '[]';
        // Supervisores
        $rolSupervisor = Role::where('name', 'Supervisor')->first();
        $supervisores = $rolSupervisor!=null? $rolSupervisor->users : '[]';
        // Lideres
        $rolLider = Role::where('name', 'Lider')->first();
        $lideres = $rolLider!=null? $rolLider->users : '[]';

        // buscar la mayor fechaProgramada en los iventarios
        return view('inventarios.programacion-semanal', [
            'puedeModificarInventarios' => $user->can('inventarios-crearModificarEliminar')? "true":"false",
            'clientes' => $clientes,
            'captadores'=> $captadores,
            'supervisores'=> $supervisores,
            'lideres'=> $lideres
        ]);
    }

    // * ##########################################################
    // *            RUTAS PARA CONSUMO DEL API REST
    // * ##########################################################

    // GET api/inventario/buscar_2
    function api_buscar_2(Request $request){
        $inventarios = Inventarios::buscar((object)[
            'idCliente' => $request->query('idCliente'),
            'fechaInicio' => $request->query('fechaInicio'),
            'fechaFin' => $request->query('fechaFin'),
            'mes' => $request->query('mes'),
            'incluirConFechaPendiente' => $request->query('incluirConFechaPendiente')
            //'idLider' => $request->query('idLider'),
        ])
            ->map('\App\Inventarios::formato_programacionIGSemanal');
        return response()->json($inventarios);
    }

    // POST api/inventario/nuevo
    function api_nuevo(Request $request){
        // solo se puede crear con permisos
        if(!Auth::user()->can('inventarios-crearModificarEliminar'))
            return response()->json([], 403);

        $validator = Validator::make($request->all(), [
            // FK
            'idLocal'=> 'required',
            // 'idCliente'=> 'required', // ignorar
            //'idJornada'=> 'required',
            // otros campos
            'fechaProgramada'=> 'required',
            //'horaLlegada'=> 'required',
            //'stockTeorico'=> 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'request'=> $request->all(),
                'errors'=> $validator->errors()
            ], 400);
        }else{
            $local = Locales::find($request->idLocal);
            if(!$local){
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> 'local no encontrado / no existe'
                ], 404);
            }

            $inventario = new Inventarios();
            $inventario->idLocal = $request->idLocal;
            // asignar la jornada que tenga por defecto el local
            $idJornada = $local->idJornadaSugerida;
            $inventario->idJornada = $idJornada;

            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->stockTeorico = $local->stock;
            $inventario->fechaStock =   $local->fechaStock;

            // Crear las dos nominas
            $nominaDia = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaDia->horaPresentacionLider = $local->llegadaSugeridaLiderDia();
            $nominaDia->horaPresentacionEquipo = $local->llegadaSugeridaPersonalDia();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaDia->dotacionTotal = $inventario->dotacionTotalSugerido();
            $nominaDia->dotacionOperadores = $inventario->dotacionOperadoresSugerido();
            // si la jornada es de "dia"(2), o "dia y noche"(4), entonces la nomina esta habilitada
            $nominaDia->habilitada = ($idJornada==2 || $idJornada==4);
            $nominaDia->idEstadoNomina = 2; // pendiente
            $nominaDia->turno = 'Día';
            $nominaDia->fechaLimiteCaptador = Inventarios::calcularFechaLimiteCaptador($request->fechaProgramada);
            $nominaDia->save();
            // se asigna el "Captador SEI" por defecto" a la nueva nomina
            $nominaDia->agregarCaptador(User::find(1), 0);

            $nominaNoche = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaNoche->horaPresentacionLider = $local->llegadaSugeridaLiderNoche();
            $nominaNoche->horaPresentacionEquipo = $local->llegadaSugeridaPersonalNoche();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaNoche->dotacionTotal = $inventario->dotacionTotalSugerido();
            $nominaNoche->dotacionOperadores = $inventario->dotacionOperadoresSugerido();
            // si la jornada es de "noche"(3), o "dia y noche"(4), entonces la nomina esta habilitada
            $nominaNoche->habilitada = ($idJornada==3 || $idJornada==4);
            $nominaNoche->idEstadoNomina = 2; // pendiente
            $nominaNoche->turno = 'Noche';
            $nominaNoche->fechaLimiteCaptador = Inventarios::calcularFechaLimiteCaptador($request->fechaProgramada);
            $nominaNoche->save();
            // se asigna el "Captador SEI" por defecto" a la nueva nomina
            $nominaNoche->agregarCaptador(User::find(1), 0);

            $inventario->nominaDia()->associate($nominaDia);
            $inventario->nominaNoche()->associate($nominaNoche);

            $resultado =  $inventario->save();

            if($resultado){
                $_inventario = Inventarios::find($inventario->idInventario);
                return response()->json(Inventarios::formato_programacionIGSemanal($_inventario), 201);
            }else{
                return response()->json([
                    'request'=> $request->all(),
                    'errors'=> $validator->errors(),
                    'resultado'=>$resultado,
                    'inventario'=>$inventario
                ], 400);
            }
        }
    }

    // GET api/inventario/{idInventario}
    function api_get($idInventario) {
        $inventario = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ])->find($idInventario);
        if ($inventario) {
            return response()->json($inventario, 200);
        } else {
            return response()->json([], 404);
        }
    }

    // PUT api/inventario/{idInventario}
    function api_actualizar($idInventario, Request $request){
        // solo se puede actualizar con permisos
        if(!Auth::user()->can('inventarios-crearModificarEliminar'))
            return response()->json([], 403);

        $inventario = Inventarios::find($idInventario);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($inventario){
            if(isset($request->fechaProgramada)){
                // actualizar fecha (si es valida) y generar un log
                // como side-efect, tambien actualiza la fechaLimiteCaptador, la fecha limite para que envie la nomina completa
                $inventario->set_fechaProgramada($request->fechaProgramada);
            }
            if(isset($request->idJornada)){
                // cambia la jornada del inventario, y cambiar el estado (habilitada) de las nominas asociadas
                $inventario->set_jornada($request->idJornada);
            }
            if(isset($request->stockTeorico)) {
                // actualizar el stock del local, y al mismo tiempo recalcular la dotacion de las nominas
                $inventario->set_stock($request->stockTeorico, Carbon::now());
            }

            // mostrar el dato tal cual como esta en la BD
            return response()->json(Inventarios::formato_programacionIGSemanal($inventario), 200);
        }else{
            return response()->json([], 404);
        }
    }

    // DELETE api/inventario/{idInventario}
    function api_eliminar($idInventario){
        // solo se puede eliminar con permisos
        if(!Auth::user()->can('inventarios-crearModificarEliminar'))
            return response()->json([], 403);

        $inventario = Inventarios::find($idInventario);
        if($inventario){
            DB::transaction(function() use($inventario){
                $inventario->delete();
                // eliminar sus jornadas
                $nominaDia = $inventario->nominaDia;
                $nominaDia->delete();
                $nominaNoche = $inventario->nominaNoche;
                $nominaNoche->delete();
                return response()->json([], 204);
            });
            return response()->json([]);
        }else{
            return response()->json([], 404);
        }
    }
}