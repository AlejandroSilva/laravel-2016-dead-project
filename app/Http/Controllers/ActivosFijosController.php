<?php

namespace App\Http\Controllers;
use DB;

use App\PreguiaDespacho;
use Illuminate\Http\Request;
//use App\Http\Requests;
//use Symfony\Component\HttpFoundation\RedirectResponse;
// Formularios y validacion
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
// Carbon
use Carbon\Carbon;
// Modelos
use App\AlmacenAF;
use App\AlmacenAF_ArticuloAF;
use App\ArticuloAF;
use App\CodigoBarra;
use App\ProductoAF;
use App\Role;
// Permisos
use Auth;

class ActivosFijosController extends Controller {

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET activo-fijo
    public function get_index(){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-verModulo'))
            return view('errors.403');

        return response()->view('logistica.activoFijo.index', [
            'almacenes' => AlmacenAF::all(),
            'permisos' => $user->permisosAsignados()
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    /** ####################### PRODUCTOS ###################### **/
    // GET api/activo-fijo/productos/buscar
    public function api_productos_buscar(Request $request){
        // todo: validar que tenga los permisos para ver los productos

        return response()->json(
            $this->_buscarProductos( (object)[
                'SKU' => $request->query('SKU'),
            ])->map('\App\ProductoAF::formato_tablaProductosAF')
        );
    }

    // POST api/activo-fijo/productos/nuevo
    public function api_productos_nuevo(){
        // validar si se tienen los permisos para agregar un producto
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-agregarProducto'))
            return response()->json([
                'error'=>['No tiene permisos para agregar un producto']
            ], 403);

        $productoNuevoRules = [
            'SKU' => 'required|max:32|unique:productos_activo_fijo',
            'descripcion' => 'required|max:60',
            'valorMercado' => 'required|integer',
        ];
        $errorMessages = [
            'SKU.required' => 'SKU requerido',
            'SKU.unique' => 'SKU ya existe',
            'descripcion.required' => 'Descripción requerido',
            'valorMercado.required' => 'valor requerido',
            'valorMercado.integer' => 'debe ser un numero',
        ];
        $validator = Validator::make(Input::all(), $productoNuevoRules, $errorMessages);
        if($validator->fails()){
            $error = $validator->messages();
            return response()->json($error, 400);
        }

        // crear el producto
        $producto = ProductoAF::create(Input::all());
        return response()->json($producto, 200);
    }

    // PUT api/activo-fijo/producto/{sku}
    public function api_producto_actualizar(Request $request, $sku){
        // validar si se tienen los permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-modificarProducto'))
            return response()->json(['error'=>'No tiene permisos para modificar un producto'], 403);

        // producto existe?
        $producto = ProductoAF::find($sku);
        if(!$producto)
            return response()->json(['sku', 'Producto no encontrado'], 400);

        // el SKU no es actualizable, porque es el PK de otras tablas
        // actualizar descripcion
        if(isset($request->descripcion))
            $producto->descripcion = $request->descripcion;
        // actualizar valor mercado (validar el precio?)
        if(isset($request->valorMercado))
            $producto->valorMercado = $request->valorMercado;

        $producto->save();
    }

    // DEL api/activo-fijo/producto/{sku}
    public function api_producto_eliminar($sku){
        // validar permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-eliminarProducto'))
            return response()->json(['error'=>'No tiene permisos para eliminar el producto'], 403);

        // producto existe?
        $producto = ProductoAF::find($sku);
        if(!$producto)
            return response()->json(['sku', 'Producto no encontrado'], 400);

        // tiene articulos?
        if($producto->articulosAF()->count()>0)
            return response()->json(
                ['articulos'=>'el producto tiene articulos asociados, eliminelos e intente nuevamente'],
                400
            );

        $producto->delete();
        return response()->json([]);
    }

    /** ####################### ARTICULOS ###################### **/

    // GET api/activo-fijo/articulos/buscar
    public function api_articulos_buscar(Request $request){
        $query = ArticuloAF::with([]);

        // va a filtrar por barras?
        $barra = $request->query('barra');
        if(isset($barra) ){
            $query
                ->where(function($q) use($barra){
                    $q->whereHas('barras', function($qq) use($barra){
                        $qq->where('barra', $barra);
                    });
                });
        }

        // va a filtrar por sku?
        $sku = $request->query('sku');
        if(isset($sku)){
            $query->where('SKU', $sku);
        }

        $articulos = $query
            ->get()
            ->sortBy('idArticuloAF');
        // Dependiendo de esto se agregan las existencias del articulo en multiples almacenes
        $responseConExistencia = $request->query('conExistencias');
        if( isset($responseConExistencia) && $responseConExistencia=="true")
            return response()->json( $articulos->map('\App\ArticuloAF::formato_conExistenciasPorAlmacen') );
        else
            return response()->json( $articulos->map('\App\ArticuloAF::formato_tablaArticulosAF') );
    }

    // POST api/activo-fijo/articulos/entregar
    public function api_articulos_entregar_a_almacen(Request $request){
        // todo: 1 se verifica que todos los articulos existan y que esten en "disponible"

        // crear una "guia de entrega", asignar su origen y su destino
        $almacenDisponible = AlmacenAF::find(1);
        $almacenDestino = AlmacenAF::find($request->almacenDestino);
        if(!$almacenDestino)
            return response()->json('Almacen no encontrado', 400);

        // si no tiene articulos seleccionados asignados, o todos tienen stock 0, no se crea la guia de despacho
        // (no es necesario contar los articulos, si no viene ninguno $sinStockPorEntregar sigue siendo falso)
        $sinStockPorEntregar = true;
        foreach ($request->articulos as $articuloTrans){
            if($articuloTrans['stockPorEntregar']!=0)
                $sinStockPorEntregar = false;
        };
        if($sinStockPorEntregar)
            return response()->json([], 200);


        // por seguridad, el proceso de cambio de stock debe ser ejecutado en el contexto de una transaccion
        DB::transaction(function() use($request, $almacenDisponible, $almacenDestino) {
            $preguiaEntrega = PreguiaDespacho::create(['descripcion' => "Entrega de articulos", 'idAlmacenOrigen' => 1,     // se entrega desde el almacen "Disponible"
                'idAlmacenDestino' => $almacenDestino->idAlmacenAF, 'fechaEmision' => Carbon::now(),]);

            // 3: enviar los articulos al almacen de destino
            foreach ($request->articulos as $articuloTrans) {
                $idArticulo = $articuloTrans['idArticuloAF'];
                $stockPorEntregar = $articuloTrans['stockPorEntregar'];

                // validar que el articulo exista
                $articulo = ArticuloAF::find($idArticulo);
                if (!$articulo) return;

                // si se esta asignado un stock de 0, no se agrega el articulo a la guia
                if ($stockPorEntregar == 0) return;

                // revisar que el stock que se esta entregando, este disponible en el almacen "Disponible"
                if ($almacenDisponible->stockArticulo($idArticulo) >= $stockPorEntregar) {
                    // Quitar el articulo del almacen Disponibles (disminuir su stock)
                    $almacenDisponible->quitarStockArticulo($idArticulo, $stockPorEntregar);

                    // Entregar el articulo al almacen de destino (aumentar su stock)
                    $almacenDestino->agregarStockArticulo($idArticulo, $stockPorEntregar);

                    // agregar el articulo a la preguia de despacho
                    $preguiaEntrega->articulos()->attach($articulo->idArticuloAF, ['stockEntregado' => $stockPorEntregar, 'stockRetornado' => 0]);
                } else {
                    //
                }
            }

            return response()->json($preguiaEntrega);
        });
    }

    // POST api/activo-fijo/articulos/transferir (INCOMPLETA)
    public function api_articulos_transferir(Request $request){
        // todo validar permisos

        // Validar que el almacen exista
        $almacenOrigen = AlmacenAF::find($request->almacenOrigen);
        $almacenDestino = AlmacenAF::find($request->almacenDestino);
        if(!$almacenOrigen || !$almacenDestino)
            return response()->json('Almacen no encontrado', 400);

        // 1) crear guia "envio transferencia" en origen
        $preguiaTransferencia = PreguiaDespacho::create([
            'descripcion' => "trans. hacia ".$almacenDestino->nombre,
            'idAlmacenOrigen' => $almacenOrigen->idAlmacenAF,
            'idAlmacenDestino' => $almacenDestino->idAlmacenAF,
            'fechaEmision' => Carbon::now(),
        ]);

        // 2) mover los articulos
        foreach($request->codigosArticulos as $cod){
            $articulo = ArticuloAF::find($cod);
            // todo validar si el articulo existe o no
            if(!$articulo) break;

            //En la "preguia original" del articulo, este se marca como "transferido"
            //$preguiaOriginal = $articulo->preguias()->where el articulo este "no entregado"

            // TODO TERMINA ESTO!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //            $preguiaOriginal->articulos()->find($cod)->

            // agregar el articulo a la guia de transferencia
            $preguiaTransferencia->articulos()->attach($articulo->codArticuloAF);

            // finalmente cambiar el articulo de almacen
            $articulo->idAlmacenAF = $almacenDestino->idAlmacenAF;
            $articulo->save();
        }

        return response()->json([]);
    }

    // POST api/activo-fijo/articulos/nuevo
    public function api_articulos_nuevo(Request $request) {
        // validar si se tienen los permisos para agregar un producto
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-agregarArticulo'))
            return response()->json(['error'=>['No tiene permisos para agregar un articulo']], 403);

        $articuloNuevoRules = [
            'SKU' => 'required|max:32|exists:productos_activo_fijo,SKU',
            'stock' => 'required|integer|min:1',
        ];
        $errorMessages = [
            'SKU.required' => 'SKU requerido',
            'SKU.exists' => 'SKU no existe',
            'stock.required' => 'stock requerido',
            'stock.integer' => 'debe ser un número',
            'stock.min' => 'debe ser como mínimo 1',
        ];
        $validator = Validator::make(Input::all(), $articuloNuevoRules, $errorMessages);
        if($validator->fails()){
            $error = $validator->messages();
            return response()->json($error, 400);
        }

        // se crea el producto en la BD
        $articulo = ArticuloAF::create(Input::all());

        // agregar al almacen disponible el "stock" entregado por el usuario
        $almacenDisponible = AlmacenAF::find(1);
        $almacenDisponible->agregarStockArticulo($articulo->idArticuloAF, Input::get('stock'));

        return response()->json(ArticuloAF::formato_tablaArticulosAF($articulo), 200);
    }

    // PUT api/activo-fijo/articulo/{idArticuloAF}
    public function api_articulo_actualizar($idArticuloAF){
        // validar si se tienen los permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-modificarArticulo'))
            return response()->json(['error'=>'No tiene permisos para modificar un articulo'], 403);

        // IMPORTANTE: cambiar el stock de un articulo es particularmente delicado:
        // ## aumentar la cantidad no se corren muchos riesgos
        // ## disminuir la cantidad es complicado: hay que revisar que no se descuenten mas de los que estan asignados
        //    a los distintos almacenes, para no generar inconsistencia con los datos, asegurar esa consistencia es
        //    complicado (por la cantidad de validaciones), asi que por ahora se deshabilita la posibilidad de cambiar
        //    el stock "a mano"
        // ##### SI SE DESEA CAMBIAR EL STOCK, HAY QUE: CAMBIAR EL STOCK DE LA TABLA ARTICULOS, Y CAMBIAR EL      ######
        // ##### STOCK DEL ARTICULO DE LA TABLA "DISPONIBLE"                                                      ######

        return response()->json([
            'error'=>['cambio manual del stock de un articulo deshabilitado, pida al administrador del sistema que realice el cambio manualmente']
        ], 503);

//        // verificar que el articulo exista
//        $articulo = ArticuloAF::find($idArticuloAF);
//        if(!$articulo){
//            return response()->json(['idArticulo', 'Articulo no encontrado'], 400);
//        }
//
//        $articuloRules = [
//            //'SKU' => 'required|max:32|unique:productos_activo_fijo',
//            'stock' => 'required|integer',
//        ];
//        $errorMessages = [
//            'stock.required' => 'stock requerido',
//            'stock.integer' => 'debe ser un numero',
//        ];
//        $validator = Validator::make(Input::all(), $articuloRules, $errorMessages);
//
//        if($validator->fails()){
//            $error = $validator->messages();
//            return response()->json($error, 400);
//        }
//
//        // si el input es valido, entonces actualizar
//        $articulo->stock = Input::get('stock');
//        $articulo->save();
//
//        return response()->json([]);
    }

    // DEL /api/activo-fijo/articulo/{idArticuloAF}
    public function api_articulo_eliminar($idArticuloAF){
        // validar si se tienen los permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-eliminarArticulo'))
            return response()->json(['error'=>'No tiene permisos para eliminar un articulo'], 403);

        // verificar que el articulo exista
        $articulo = ArticuloAF::find($idArticuloAF);
        if(!$articulo){
            return response()->json(['idArticulo', 'Articulo no encontrado'], 400);
        }

        // no se puede eliminar un articulo si tiene barras asociadas
        if($articulo->barras()->count()>0)
            return response()->json(['barras'=>'el Articulo tiene Códigos de Barra asociadas, eliminelos e intente nuevamente'], 400);

        // no se puede eliminar un articulo si este ha sido agregado a una pre-guia de despacho
        if($articulo->preguias->count() > 0)
            return response()->json(['barras'=>'el Articulo tiene Pre-guias asociadas, no puede ser eliminado'], 400);

        // no se puede eliminar un articulo si tiene stock asignado a un almacen
        $erroresDeAlmacenes = $articulo->existencias_en_almacenes
            ->map(function($almacenArticulo){
                $pivot = $almacenArticulo->pivot;
                // si el producto esta en un almacen que no sea "disponible", entonces se cuenta como un error
                if($pivot->idAlmacenAF !=1 ){
                    $almacen = AlmacenAF::find($pivot->idAlmacenAF);
                    return ["Tiene stock asignado de $pivot->stockActual en el almacen '$almacen->nombre' (id:$pivot->idAlmacenAF)"];
                }else
                    return null;
            })
            ->filter(function($error){
                return $error!=null;
            });

        // si existe stock del articulo algun almacen (distinto a "disponible"), entonces no se puede eliminar
        if( $erroresDeAlmacenes->count()>0 ){
            return response()->json( array_values( $erroresDeAlmacenes->toArray()), 400 );
        }

        // si llega hasta aca, eliminar el stock en "disponible", y luego eliminar el producto
        $almacenDisponible = AlmacenAF::find(1);
        $stockEnDisponible = $almacenDisponible->stockArticulo($articulo->idArticuloAF);
        $almacenDisponible->quitarStockArticulo($articulo->idArticuloAF, $stockEnDisponible);

        $articulo->delete();

        // TODO: lanza un error 500 cuando se elimina un articulo que tenga haya sido
        return response()->json([]);
    }

    /** ######################### BARRAS ####################### **/

    // POST api/activo-fijo/barras/nuevo
    public function api_barra_nueva(Request $request){
        // validar si se tienen los permisos para agregar un producto
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-agregarBarra'))
            return response()->json(['error'=>['No tiene permisos para agregar un codigo de barra']], 403);

        $barraNuevoRules = [
            // debe existir el articulo
            'idArticuloAF' => 'required|exists:acticulos_activo_fijo,idArticuloAF',
            // la barra debe ser unica
            'barra' => 'required|string|unique:codigos_barra,barra'
        ];
        $errorMessages = [
            'idArticuloAF.required' => 'Articulo requerido',
            'idArticuloAF.exists' => 'Articulo no existe',
            'barra.required' => 'barra requerido',
            'barra.string' => 'debe ser un string',
            'barra.unique' => 'ya existe un producto con ese código',
        ];
        $validator = Validator::make(Input::all(), $barraNuevoRules, $errorMessages);
        if($validator->fails()){
            $error = $validator->messages();
            return response()->json($error, 400);
        }

        // crear la barra
        CodigoBarra::create(Input::all());

        // retornar el articulo con sus barras actualizadas
        $articulo = ArticuloAF::find(Input::get('idArticuloAF'));
        return response()->json(ArticuloAF::formato_tablaArticulosAF($articulo), 200);
    }

    // DELETE api/activo-fijo/barra/{codBarra}
    public function api_barra_eliminar($codBarra){
        // validar si se tienen los permisos
        $user = Auth::user();
        if(!$user || !$user->can('activoFijo-eliminarBarra'))
            return response()->json(['error'=>'No tiene permisos para eliminar.'], 403);

        // verificar que el articulo exista
        $codigo = CodigoBarra::find($codBarra);
        if(!$codigo){
            return response()->json(['error', 'Código de barra no encontrado'], 400);
        }

        $codigo->delete();
        return response()->json([]);
    }

    /** ####################### ALMACENES ###################### **/

    // GET api/activo-fijo/almacenes/buscar
    public function api_almacenes_buscar(){
        // todo validaciones, y mejor mensaje de error
        return response()->json( AlmacenAF::all() );
    }

    // POST api/activo-fijo/almacen/nuevo
    public function api_almacen_nuevo(Request $request){
        // todo: validar permisos, validar que los campos sean validos (por ahora solo se hace en el frontend)
        AlmacenAF::create([
            'idUsuarioResponsable' => $request->idResponsable,
            'nombre'=>$request->nombre
        ]);
        return response()->json('ok');
    }

    // GET api/activo-fijo/almacen/{idAlmacen}/articulos
    public function api_almacen_articulos($idAlmacen){
        // retornar todos los articulos asociados a algun almance
        $query = AlmacenAF_ArticuloAF::with([]);

        if($idAlmacen!=0){
            $query->where('idAlmacenAF', $idAlmacen);
        }

        return response()->json(array_values(
            $articulos = $query->get()
                ->map(function($almArt){
                    return AlmacenAF_ArticuloAF::formato_tablaArticulos($almArt);
                })
                ->sort(function($a, $b){
                    // puede existir el caso en donde un articulo no tenga codigo de barras
                    $barra_A = isset($a['barras'][0])? $a['barras'][0] : '';
                    $barra_B = isset($b['barras'][0])? $b['barras'][0] : '';
                    // ordenados por sku, luego por el primer codigo de barra de cada uno, y finalmente por almacen
                    return strcmp($a['SKU'], $b['SKU'])
                        ?: strcmp($barra_A, $barra_B)
                            ?: strcmp($a['idAlmacenAF'], $b['idAlmacenAF']);
                })
                ->toArray()
        ));
    }

    /** ####################### PRE-GUIAS ###################### **/

    // GET api/activo-fijo/preguias/buscar
    public function api_preguias_buscar(Request $request){
        return response()->json(
            $this->_buscarPreguias( (object)[
                'idAlmacenAF' => $request->query('almacen'),
            ])->map('\App\PreguiaDespacho::formato_tabla')
        );
    }

    // GET api/activo-fijo/preguia/{idPreguia}
    public function api_preguia_fetch($idPreguia){
        // todo: validar permisos
        $preguia = PreguiaDespacho::find($idPreguia);
        if(!$preguia)
            return response()->json([], 200);
        else
            return response()->json( PreguiaDespacho::formato_retornoArticulos($preguia) );
    }

    // POST api/activo-fijo/preguia/{idPreguia}/devolver
    public function api_preguia_devolver(Request $request, $idPreguia){
        // todo validar que tenga los permisos para retornar en esta preguia

        // la preguia indicada existe?
        $preguia = PreguiaDespacho::find($idPreguia);
        if(!$preguia)
            return response()->json('Preguia no encontrada', 400);

        // por seguridad, el proceso de cambio de stock debe ser ejecutado en el contexto de una transaccion
        DB::transaction(function() use($request, $preguia){
            $almacenDestino = $preguia->almacenDestino;
            $almacenDisponible = AlmacenAF::find(1);

            // mover cada uno de los articulos al almacen "Disponible"
            foreach ($request->articulos as $articuloRetorno) {
                $idArticulo = $articuloRetorno['idArticuloAF'];
                $stockParaRetornar = $articuloRetorno['stockParaRetornar'];

                $articulo = ArticuloAF::find($idArticulo);
                // todo validar si el articulo existe o no
                if (!$articulo) break;

                // revisar que el stock que se esta retornando, sea igual o menor al stock que esta en el almacen
                if ($almacenDestino->stockArticulo($idArticulo) >= $stockParaRetornar) {
                    // quitar el stock en el almacen de destino
                    $almacenDestino->quitarStockArticulo($idArticulo, $stockParaRetornar);

                    // agregar el stock en el almacen 'Disponible'
                    $almacenDisponible->agregarStockArticulo($idArticulo, $stockParaRetornar);

                    // actualizar el stockRetornado en la preguia
                    $pivot = $preguia->articulos()->find($idArticulo)->pivot;
                    $pivot->stockRetornado += $stockParaRetornar;
                    $pivot->save();
                    //                $preguia->articulos()->updateExistingPivot($idArticulo, [
                    //                    'stockRetornado' => $stockParaRetornar
                    //                ]);

                } else {
                    // ERROR: se esta tratanto de retornar mas tock del que actualmente se tiene
                    // ... no hacer nada con el articulo ...
                }
            }
        });

        // TODO: 3 cambiar el estado de la guia a "retornada"
        return response()->json([]);
    }

    /** ######################### OTROS ######################## **/

    // GET api/activo-fijo/responsables/buscar
    public function api_responsables_buscar(){
        // Todo: Hacer esto bien, definir el permiso, y las condiciones para ser "responsable de inventario" 
        $rolLider = Role::where('name', 'Lider')->first();
        return response()->json(
            $rolLider->users->map('\App\User::formatearMinimo')
        );
    }
    
    /**
     * Privadas
     */
    private function _buscarProductos($peticion){
        $query = ProductoAF::with([]);

        // buscar por SKU
        if(isset($peticion->SKU)){
            $query->where('SKU', $peticion->SKU);
        }

        return $query->get();
    }
    private function _buscarArticulos($peticion){
        $query = ArticuloAF::with([]);

        // buscar por Almacen
        if(isset($peticion->idAlmacenAF) && $peticion->idAlmacenAF!="0"){
            $query->where('idAlmacenAF', $peticion->idAlmacenAF);
        }
        return $query->get();
    }
    private function _buscarPreguias($peticion){
        $query = PreguiaDespacho::with([]);

        // buscar por Almacen
        $idAlmacen = $peticion->idAlmacenAF;
        if(isset($idAlmacen) && $idAlmacen!="0"){
            $query
                ->where('idAlmacenOrigen', $idAlmacen)
                ->orWhere('idAlmacenDestino', $idAlmacen);
        }
        $query->orderBy('idPreguia', 'desc');
        return $query->get();
    }

}