<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Redirect;
// PHP Excel
use PHPExcel;
use PHPExcel_IOFactory;
// Modelos
use App\Clientes;
use App\Inventarios;
use App\Locales;
use App\Nominas;
// Auth
use App\Role;
use Auth;


class InventariosController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET programacionIG/
    public function showProgramacionIndex(){
        return view('operacional.programacionIG.programacionIG-index');
    }

    // GET programacionIG/mensual
    public function showProgramacionMensual(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
            return view('errors.403');

        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.programacionIG.programacionIG-mensual', [
            'puedeAgregarInventarios'   => $user->can('programaInventarios_agregar')? "true":"false",
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientesWithLocales,
        ]);
    }

    // GET programacionIG/semanal
    public function showProgramacionSemanal(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaInventarios_ver'))
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
        return view('operacional.programacionIG.programacionIG-semanal', [
            'puedeModificarInventarios' => $user->can('programaInventarios_modificar')? "true":"false",
            'clientes' => $clientes,
            'captadores'=> $captadores,
            'supervisores'=> $supervisores,
            'lideres'=> $lideres
        ]);
    }

    // GET inventario
    function showIndex(){
        return view('operacional.inventario.inventario-index');
    }

    // GET inventario/lista
    function showLista(){
        return view('operacional.inventario.inventario-lista');
    }

    // GET inventario/nuevo
    function showNuevo(){
        $clientesWithLocales = Clientes::allWithSimpleLocales();
        return view('operacional.inventario.inventario-nuevo', [
            'clientes' => $clientesWithLocales
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // POST api/inventario/nuevo
    function api_nuevo(Request $request){
        $validator = Validator::make($request->all(), [
            // FK
            'idLocal'=> 'required',
            // 'idCliente'=> 'required', // ignorar
            //'idJornada'=> 'required',
            // otros campos
            'fechaProgramada'=> 'required',
            //'horaLlegada'=> 'required',
            //'stockTeorico'=> 'required',
            //'dotacionAsignadaTotal'=> 'required'
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
            // asignar la jornada entregada por parametros, o la que tenga por defecto el local 
            if($request->idJornada) {
                $inventario->idJornada = $request->idJornada;
            }else{
                $inventario->idJornada = $local->idJornadaSugerida;
            }
            $inventario->fechaProgramada = $request->fechaProgramada;
            $inventario->dotacionAsignadaTotal = $local->dotacionSugerida();
            $inventario->stockTeorico = $local->stock;
            $inventario->fechaStock =   $local->fechaStock;

            // Crear las dos nominas
            $nominaDia = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaDia->horaPresentacionLider = $local->llegadaSugeridaLiderDia();
            $nominaDia->horaPresentacionEquipo = $local->llegadaSugeridaPersonalDia();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaDia->dotacionAsignada = $local->dotacionSugerida();
            $nominaDia->dotacionCaptador1 = 0;
            $nominaDia->dotacionCaptador2 = 0;
            $nominaDia->horaTermino = '';
            $nominaDia->horaTerminoConteo = '';
            $nominaDia->save();

            $nominaNoche = new Nominas();
            // Lider, Captador1, Captador2 no se definen
            $nominaNoche->horaPresentacionLider = $local->llegadaSugeridaLiderNoche();
            $nominaNoche->horaPresentacionEquipo = $local->llegadaSugeridaPersonalNoche();
            // Todo: la dotacion sugerida deberia dividirse en dos cuando la jornada sea doble:
            $nominaNoche->dotacionAsignada = $local->dotacionSugerida();
            $nominaNoche->dotacionCaptador1 = 0;
            $nominaNoche->dotacionCaptador2 = 0;
            $nominaNoche->horaTermino = '';
            $nominaNoche->horaTerminoConteo = '';
            $nominaNoche->save();

            $inventario->nominaDia()->associate($nominaDia);
            $inventario->nominaNoche()->associate($nominaNoche);

            $resultado =  $inventario->save();

            if($resultado){
                return response()->json(
                    $inventario = Inventarios::with([
                        'local.cliente',
                        'local.formatoLocal',
                        'local.direccion.comuna.provincia.region',
                        'nominaDia',
                        'nominaNoche'
                    ])->find($inventario->idInventario)
                    , 201);
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
    function api_get($idInventario){
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
        if($inventario){
            return response()->json($inventario, 200);
        }else{
            return response()->json([], 404);
        }
    }

    // PUT api/inventario/{idInventario}
    function api_actualizar($idInventario, Request $request){
        $inventario = Inventarios::find($idInventario);
        // si no existe retorna un objeto vacio con statusCode 404 (not found)
        if($inventario){
            if(isset($request->fechaProgramada))
                $inventario->fechaProgramada = $request->fechaProgramada;
            if(isset($request->dotacionAsignadaTotal))
                $inventario->dotacionAsignadaTotal = $request->dotacionAsignadaTotal;
            if(isset($request->idJornada))
                $inventario->idJornada = $request->idJornada;
            if(isset($request->stockTeorico))
                $inventario->stockTeorico = $request->stockTeorico;

            $resultado = $inventario->save();

            if($resultado) {
                // mostrar el dato tal cual como esta en la BD
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
                    ])->find($inventario->idInventario),
                    200);
            }else{
                return response()->json([
                    'request'=>$request->all(),
                    'resultado'=>$resultado,
                    'inventario'=>$inventario
                ], 400);
            }
        }else{
            return response()->json([], 404);
        }
    }

    // DELETE api/inventario/{idInventario}
    function api_eliminar($idInventario, Request $request){
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

        }else{
            return response()->json([], 404);
        }
    }

    // GET api/inventario/mes/{annoMesDia}
    function api_getPorMesYCliente($annoMesDia, $idCliente){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        $query = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes]);

        if($idCliente==0){
            // No se realiza un filtro por clientes
            $inventarios = $query->get();
            return response()->json($inventarios->toArray(), 200);
        }
        else{
            // Se filtran por cliente
            $inventarios = $query
                ->whereHas('local', function($query) use ($idCliente){
                    $query->where('idCliente', '=', $idCliente);
                })
                ->get();
            return json_encode($inventarios->toArray());
        }
    }

    // GET api/inventario/{fecha1}/al/{fecha2}
    function api_getPorRango($annoMesDia1, $annoMesDia2){
        $inventarios = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            ->get();

        // se modifican algunos campos para ser tratados mejor en el frontend
        $inventariosMod = array_map(function($inventario){
            $local = $inventario['local'];
            $local['nombreCliente'] = $local['cliente']['nombreCorto'];
            $local['nombreComuna'] = $local['direccion']['comuna']['nombre'];
            $local['nombreProvincia'] = $local['direccion']['comuna']['provincia']['nombre'];
            $local['nombreRegion'] = $local['direccion']['comuna']['provincia']['region']['numero'];
            $inventario['local'] = $local;
            return $inventario;
        }, $inventarios->toArray());

        return response()->json($inventariosMod, 200);
    }

    // GET api/inventario/{fecha1}/al/{fecha2}/cliente/{idCliente}
    function api_getPorRangoYCliente($annoMesDia1, $annoMesDia2, $idCliente){
        $query = Inventarios::with([
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche',
            'nominaDia.lider',
            'nominaNoche.lider',
            'nominaDia.captador',
            'nominaNoche.captador',
        ])
            ->where('fechaProgramada', '>=', $annoMesDia1)
            ->where('fechaProgramada', '<=', $annoMesDia2)
            ->orderBy('fechaProgramada', 'asc');

        if($idCliente==0){
            // No se realiza un filtro por clientes
            $inventarios = $query->get();
            return response()->json($inventarios->toArray(), 200);
        }
        else{
            // Se filtran por cliente
            $inventarios = $query
                ->whereHas('local', function($query) use ($idCliente){
                    $query->where('idCliente', '=', $idCliente);
                })
                ->get();
            return json_encode($inventarios->toArray());
        }
    }


    /**
     * ##########################################################
     * Descarga de documentos
     * ##########################################################
     */
    // GET /pdf/inventarios/{mes}/cliente/{idCliente}
    public function descargarPDF_porMes($annoMesDia, $idCliente){
        $fecha = explode('-', $annoMesDia);
        $anno = $fecha[0];
        $mes  = $fecha[1];

        // inventarios que se desean mostrar
        $inventarios = Inventarios::with([
            //'local',
            'local.cliente',
            'local.formatoLocal',
            'local.direccion.comuna.provincia.region',
            'nominaDia',
            'nominaNoche'
        ])
            ->whereRaw("extract(year from fechaProgramada) = ?", [$anno])
            ->whereRaw("extract(month from fechaProgramada) = ?", [$mes])
            ->join('locales', 'locales.idLocal', '=', 'inventarios.idLocal')
            ->orderBy('fechaProgramada', 'ASC')
            ->orderBy('locales.stock', 'DESC')
            ->get();

        $inventariosHeader = ['Fecha', 'Cliente', 'CECO', 'Local', 'Región', 'Comuna', 'Stock', 'Dotación Total'];
        $inventariosArray = array_map(function($inventario){
            return [
                $inventario['fechaProgramada'],
                $inventario['local']['cliente']['nombreCorto'],
                $inventario['local']['numero'],
                $inventario['local']['nombre'],
                $inventario['local']['direccion']['comuna']['provincia']['region']['numero'],
                $inventario['local']['direccion']['comuna']['nombre'],
                $inventario['local']['stock'],
                $inventario['dotacionAsignadaTotal'],
            ];
        }, $inventarios->toArray());

        // Nuevo archivo
        $workbook = new PHPExcel();  // workbook
        $sheet = $workbook->getActiveSheet();
        // agregar datos
        $sheet->setCellValue('A1', 'Programación mensual');
        $sheet->fromArray($inventariosHeader, NULL, 'A4');
        $sheet->fromArray($inventariosArray,  NULL, 'A5');

        // guardar
        $excelWritter = PHPExcel_IOFactory::createWriter($workbook, "Excel2007");
        $randomFileName = "pmensual_".md5(uniqid(rand(), true)).".xlxs";
        $excelWritter->save($randomFileName);

        // entregar la descarga al usuario
        return response()->download($randomFileName, "programacion $annoMesDia.xlsx");
    }

    // GET /pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}
    public function descargarPDF_porRango($fechaInicial, $fechaFinal, $idCliente){
        return response()->json(['msg'=>'no implementado'], 501);
    }
}