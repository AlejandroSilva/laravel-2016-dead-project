<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
// Modelos
use App\AlmacenAF;
use App\Locales;
use App\ProductoAF;
use App\Role;
use League\Flysystem\Adapter\Local;

class ActivosFijosController extends Controller {

    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */

    // GET activo-fijo/local/{idLocal}
    public function get_indexLocal($idLocal){
        $local = Locales::find($idLocal);
        $cliente = $local->cliente;

        if(!$local)
            return response()->view('errors.errorConMensaje', [
                'titulo'=>'Local no encontrado',
                'descripcion'=>'El local que esta buscando no ha sido encontrado'
            ]);

        return response()->view('logistica.activoFijo.indexLocal', [
            'local' => $local,
            'cliente'=>$cliente,
            'almacenes' => $local->almacenesAF
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
    // GET api/activo-fijo/cliente/{idCliente}/almacenesAF
    public function api_almacenes(){
        // por ahora no es necesario (se esta entregando directamente a la vista
        return response()->json([], 501);
    }
    
    // GET api/activo-fijo/cliente/{idCliente}/responsables
    public function api_responsables($idCliente){
        // Todo: Hacer esto bien, definir el permiso, y las condiciones para ser "responsable de inventario" 
        $rolLider = Role::where('name', 'Lider')->first();
        return response()->json(
            $rolLider->users->map('\App\User::formatearMinimo')
        );
    }
    
    // GET api/activo-fijo/local/{idLocal}/buscar-productos
    public function api_local_buscar_productos(Request $request, $idLocal){
        // todo: validar que exista, y que tenga los permisos para ver los productos
        if(!Locales::find($idLocal))
            return response()->json([], 400);

        return response()->json(
            $this->buscar( (object)[
                'idLocal' => $idLocal,
                'barra' => $request->query('barra'),
                'idAlmacenAF' => $request->query('almacen')
            ])->map('\App\ProductoAF::formato_tablaProductosAF')
        );
    }

    // POST api/activo-fijo/local/{idLocal}/transferir-productos
    public function api_local_transferir_productos(Request $request){
        // todo validar permisos
        $idProductos = $request->idProductos;
        foreach($idProductos as $id){
            $producto = ProductoAF::find($id);
            // todo validar..
            if(!$producto) return;

            $producto->idAlmacenAF= $request->almacenDestino;
            $producto->save();
        }

        return response()->json($idProductos);
    }

    // GET api/activo-fijo/local/{idLocal}/almacen
    public function api_local_almacenes($idLocal){
        // todo validaciones, y mejor mensaje de error
        $local = Locales::find($idLocal);
        if(!$local)
            return response()->json([], 400);

        return response()->json($local->almacenesAF);
    }

    // POST api/activo-fijo/local/{idLocal}/almacen
    public function api_almacen_nuevo($idLocal, Request $request){
        $local = Locales::find($idLocal);
        if(!$local)
            return response()->json('Local no existe', 400);

        // todo: validar permisos, validar que los campos sean validos (por ahora solo se hace en el frontend)
        $local->almacenesAF()->create([
            'idLocal'=>$idLocal,
            'nombre'=>$request->nombre,
            'idUsuarioResponsable' => $request->idResponsable
        ]);
        return response()->json('ok');
    }

    // GET api/activo-fijo/almacen/{idAlmacen}/preguias
    public function api_almacen_preguias(){
        return response()->json([], 501);
    }
    
    /**
     * Privadas
     */
    private function buscar($peticion){
        $query = ProductoAF::with([]);

        // buscar por Local
        if(isset($peticion->idLocal)){
            $query->where('idLocal', $peticion->idLocal);
        }

        // Buscar por el id de un almacen
        $idAlmacen = $peticion->idAlmacenAF;
        if(isset($idAlmacen) && $idAlmacen!=0){
            $query->where('idAlmacenAF', $idAlmacen);
        }

        // buscar por barra
        if(isset($peticion->barra)){
            $query->where('codActivoFijo', $peticion->barra);
        }

        return $query->get();
    }
}