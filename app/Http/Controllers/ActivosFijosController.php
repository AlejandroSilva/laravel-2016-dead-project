<?php

namespace App\Http\Controllers;

use App\ArticuloAF;
use App\CodigoBarra;
use App\PreguiaDespacho;
use Illuminate\Http\Request;
use App\Http\Requests;
// Carbon
use Carbon\Carbon;
// Modelos
use App\AlmacenAF;
use App\Locales;
use App\ProductoAF;
use App\Role;
use League\Flysystem\Adapter\Local;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ActivosFijosController extends Controller {

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET activo-fijo
    public function get_index(){
        return response()->view('logistica.activoFijo.index', [
            'almacenes' => AlmacenAF::all()
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */

    // GET api/activo-fijo/productos/buscar
    public function api_productos_buscar(Request $request){
        // todo: validar que exista, y que tenga los permisos para ver los productos

        return response()->json(
            $this->_buscarProductos( (object)[
                'SKU' => $request->query('SKU'),
            ])->map('\App\ProductoAF::formato_tablaProductosAF')
        );
    }

    // GET api/activo-fijo/articulos/buscar
    public function api_articulos_buscar(Request $request){
        // todo: validar que exista, y que tenga los permisos para ver los articulos
        return response()->json(
            $this->_buscarArticulos( (object)[
                'idAlmacenAF' => $request->query('almacen'),
            ])->map('\App\ArticuloAF::formato_tablaArticulosAF')
        );
    }

    // GET api/activo-fijo/articulos/buscar-barra
    public function api_articulos_buscarBarra(Request $request){
        $barra = $request->query('barra');
        if(!isset($barra))
            return response()->json([], 400);

        $codigobarra = CodigoBarra::find($barra);
        if(!$codigobarra)
            return response()->json(null);

        return response()->json( ArticuloAF::formato_tablaArticulosAF( $codigobarra->articuloAF) );
    }

    // POST api/activo-fijo/articulos/entregar
    public function api_articulos_entregar_a_almacen(Request $request){
        // todo: 1 se verifica que todos los articulos existan y que esten en "disponible"

        // 2: crear una "guia de entrega" en el destino
        $almacenDestino = AlmacenAF::find($request->almacenDestino);
        if(!$almacenDestino)
            return response()->json('Almacen no encontrado', 400);

        $preguiaEntrega = PreguiaDespacho::create([
            'descripcion' => "entrega de articulos",
            'idAlmacenOrigen' => 1,         // disponible
            'idAlmacenDestino' => $almacenDestino->idAlmacenAF,
            'fechaEmision' => Carbon::now(),
        ]);

        // 3: enviar los articulos al almacen de destino
        foreach($request->codigosArticulos as $cod){
            $articulo = ArticuloAF::find($cod);
            // todo validar si el articulo existe o no
            if(!$articulo) return;

            // cambiar de almacen
            $articulo->idAlmacenAF = $almacenDestino->idAlmacenAF;
            $articulo->save();

            // agregar a la lista de productos
            $preguiaEntrega->articulos()->attach($articulo->codArticuloAF);
        }

        return response()->json($preguiaEntrega);
    }

    // POST api/activo-fijo/articulos/transferir
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
        $codigos = $request->codigosArticulos;
        // todo validar permisos
        $preguia = PreguiaDespacho::find($idPreguia);
        if(!$preguia)
            return response()->json('Preguia no encontrada', 400);

        // 1 mover cada uno de los articulos a "disponible"
        foreach($codigos as $cod){
            $articulo = ArticuloAF::find($cod);
            // todo validar si el articulo existe o no
            if(!$articulo) break;

            // cambiar de almacen hacia "disponible"
            $articulo->idAlmacenAF = 1;
            $articulo->save();

            // 2 marcar los articulos de la guia como "retornado" (1) en la tabla pivot
            $preguia->articulos()->updateExistingPivot($cod, [
                'estado'=>1
            ], true);
        }

        // TODO: 3 cambiar el estado de la guia a "retornada"
        return response()->json($codigos);
    }

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