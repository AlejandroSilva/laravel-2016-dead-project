<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class ClientesController extends Controller {
    // GET admin/mantenedor-locales
    function show_mantenedor(){
        return view('admin.index-mantenedor-clientes', [
            'clientes' => Clientes::all()
        ]);
    }

    function form_nuevo(Request $request){
        // sin validaciones, sin revisiones, sin nada, hay que hacerlo rapido
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|unique:clientes|max:50|min:2',
            'nombreCorto' => 'required|unique:clientes|max:10|min:2',
        ], [
            "required" => ':attribute es obligatorio',
            "unique" => ':attribute debe ser unico',
            "max" => ':attribute debe tener un máximo de :max caracteres',
            "min" => ':attribute debe tener un minimo de :min caracteres'
        ]);

        if($validator->fails()){
            return redirect('admin/mantenedor-clientes')
                ->withErrors($validator, 'nuevo')
                ->withInput($request->all());
        }else{
            Clientes::insert([
                'nombre' => $request->nombre,
                'nombreCorto' => $request->nombreCorto,
            ]);
            return redirect('admin/mantenedor-clientes');
        }
    }

    // PUT api/cliente/{idCliente}
    function form_editar($idCliente, Request $request){
        // sin validaciones ni nada, me pidieron trabajar "rapido"
        $cliente = Clientes::find($idCliente);
        if(!$cliente)
            return redirect('admin/mantenedor-clientes');

        $validator = Validator::make($request->all(), [
            //unique:table,column,except,idColumn
            'nombre'      => "required|unique:clientes,nombre,$idCliente,idCliente|max:50|min:2",
            'nombreCorto' => "required|unique:clientes,nombreCorto,$idCliente,idCliente|max:10|min:2",
        ], [
            "required" => ':attribute es obligatorio',
            "unique" => ':attribute debe ser unico',
            "max" => ':attribute debe tener un máximo de :max caracteres',
            "min" => ':attribute debe tener un minimo de :min caracteres'
        ]);
        if($validator->fails()){
            return redirect('admin/mantenedor-clientes')
                ->withErrors($validator, 'nuevo');
        }else{
            $cliente->nombre = $request->nombre;
            $cliente->nombreCorto = $request->nombreCorto;
            $cliente->save();
            return redirect('admin/mantenedor-clientes');
        }
    }

    // DELETE api/cliente/{idCliente}
    function api_eliminar($idCliente){
        // sin validaciones ni nada, trabajar "rapido"
        $cliente = Clientes::find($idCliente);
        if(!$cliente || $cliente->locales->count()>0)
            return redirect('admin/mantenedor-clientes');

        $cliente->delete();
        return redirect('admin/mantenedor-clientes');
    }
}
