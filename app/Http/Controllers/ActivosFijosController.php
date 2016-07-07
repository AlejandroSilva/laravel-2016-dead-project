<?php

namespace App\Http\Controllers;

use App\ArticuloAF;
use App\CodigoBarra;
use Illuminate\Http\Request;
use App\Http\Requests;
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
                //'barra' => $request->query('barra'),
                //'codArticuloAF' => $request->query('codArticuloAF'),
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

    // POST api/activo-fijo/articulos/transferir
    public function api_articulos_transferir(Request $request){
        // todo validar permisos
        $codigosArticulos = $request->codigosArticulos;
        foreach($codigosArticulos as $cod){
            $articulo = ArticuloAF::find($cod);
            // todo validar..
            if(!$articulo) return;

            $articulo->idAlmacenAF = $request->almacenDestino;
            $articulo->save();
        }

        return response()->json($codigosArticulos);
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
    public function api_preguias_buscar($idAlmacen){
        return response()->json([], 501);
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

        // buscar por Codigo Articulo
//        if(isset($peticion->codArticuloAF)){
//            $query->where('codArticuloAF', $peticion->codArticuloAF);
//        }
        // buscar por Almacen
        if(isset($peticion->idAlmacenAF) && $peticion->idAlmacenAF!="0"){
            $query->where('idAlmacenAF', $peticion->idAlmacenAF);
        }
        return $query->get();
    }
}