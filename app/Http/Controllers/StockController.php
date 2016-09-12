<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Carbon\Carbon;
// PHPExcel
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
// Subir archivos
use Input;
// Modelos
use App\Clientes;
use App\Locales;

class StockController extends Controller {
    /**
     * ##########################################################
     * Rutas que generan vistas
     * ##########################################################
     */
    // GET admin/stock
    function show_mantenedorStock(){
        $user = Auth::user();
        if(!$user || !$user->can('admin-actualizarStock'))
            return view('errors.403');

        return view('admin.mantenedorStock', [
            'clientes' => Clientes::all()
        ]);
    }

    /**
     * ##########################################################
     * Rutas para consumo del API REST
     * ##########################################################
     */
    // GET stock/leerArchivo        --      RUTA PARA HACER PRUEBAS, ELIMINAR
    function api_leerArchivo(){
        $tableData = $this->leerArchivo(public_path().'/actualizarStock/stockFCV-2016-05-31.xlsx');

        $data = collect($tableData);
        $hoy = Carbon::now()->format("Y-m-d");
        return response()->json(
            $data->map(function($row) use($hoy){
                // comoo es una ruta de prueba, se deja el idCliente=2 FCV
                return $this->actualizarLocal(2, $row['numero'], $row['stock'], $hoy);
            })
        );
    }
    
    // POST stock/pegar
    function api_pegarDatos(Request $request){
        // revisar permisos
        $user = Auth::user();
        if(!$user || !$user->can('admin-actualizarStock'))
            return response()->json(['error' => 'No tiene permisos para realizar esta acción'], 403);

        // revisar que el cliente este fijado y exista
        if(!$request->idCliente)
            return response()->json(['error' => 'Debe indicar un cliente.'], 400);
        $idCliente = $request->idCliente;
        $cliente = Clientes::find($idCliente);
        if(!$cliente)
            return response()->json(['error' => 'El cliente seleccionado no es valido.'], 400);

        // Todo: validar que 'datos' sea un arreglo valido
        $datos = collect($request->datos);

        $hoy = Carbon::now()->format("Y-m-d");
        return response()->json(
            $datos->map(function($row) use ($cliente, $hoy){
                // el cliente se pasa por parametro, y la fechaStock, es la fecha actual
                return $this->actualizarLocal($cliente, $row['numero'], $row['stock'], $hoy);
            })
        );
    }

    // POST stock/upload
    function api_uploadArchivo(Request $request){
        // revisar permisos
        $user = Auth::user();
        if(!$user || !$user->can('admin-actualizarStock'))
            return response()->json(['error' => 'No tiene permisos para realizar esta acción'], 403);

        // revisar que el cliente este fijado y exista
        if(!$request->idCliente)
            return response()->json(['error' => 'Debe indicar un cliente.'], 400);
        $idCliente = $request->idCliente;
        $cliente = Clientes::find($idCliente);
        if(!$cliente)
            return response()->json(['error' => 'El cliente seleccionado no es valido.'], 400);

        // revisar que el archivo este adjunto
        if (!$request->hasFile('stockExcel'))
            return response()->json(['error' => 'Debe adjuntar el archivo con el stock actualizado.'], 400);

        // revisar que el archivo sea valido
        $archivo = $request->file('stockExcel');
        if (!$archivo->isValid())
            return response()->json(['error' => 'El archivo enviado no es valido, intentelo nuevamente.'], 400);

        // mover el archivo junto a los otros stocks enviados
        $ahora = Carbon::now()->format("Y-m-d h:i:s");
        $nombreOriginal = $archivo->getClientOriginalName();
        $fileName = "$ahora $cliente->nombreCorto -- $nombreOriginal";

        // guardar el archivo en una carpeta publica, y cambiar los permisos para que el grupo pueda modifiarlos
        $archivo->move( public_path().'/actualizarStock/uploads', $fileName);
        chmod(public_path().'/actualizarStock/uploads/'.$fileName, 0774);   // 0744 por defecto
        
        // Procesar el archivo, y actualizar el stock
        try{
            $tableData = $this->leerArchivo(public_path().'/actualizarStock/uploads/'.$fileName);
        } catch (ErrorException $e) {
            return response()->json(['error'=>'Error al procesar el archivo, verifique que el formato sea el valido']);
        }
        $data = collect($tableData);
        $hoy = Carbon::now()->format("Y-m-d");
        return response()->json(
            $data->map(function($row) use ($cliente, $hoy){
                // el cliente se pasa por parametro, y la fechaStock, es la fecha actual
                return $this->actualizarLocal($cliente, $row['numero'], $row['stock'], $hoy);
            })
        );
    }


    /**
     * ##########################################################
     * funciones privadas
     * ##########################################################
     */

    private function actualizarLocal($cliente, $numero, $stock, $fechaStock){
        // todo: validar que el stock y la $fechaStock sean validos

        $local = Locales::where('idCliente', $cliente->idCliente)
            ->where('numero', $numero)->first();
        // validar que el local exista
        if(!$local)
            return [
                'cliente'=>$cliente->nombreCorto,
                'local'=>$numero,
                'error' => 'El local buscado no se encuentra.',
                'estado' => '',
                'inventarios' => []
            ];
        else{
            // actualizar stock del local, el mismo metodo actualiza el stock de los inventarios asociados
            return $local->set_stock($stock, $fechaStock);
        }
    }

    private function leerArchivo($inputFileName){
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
//            dd($inputFileName);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        //  Loop through each row of the worksheet in turn
        $tableData = [];
        // iniciar el 2, para saltarse el titulo
        for ($row = 2; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            //$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $A = $sheet->getCell("A$row");
            $B = $sheet->getCell("B$row");
            //$C = $sheet->getCell("C$row");
            //$D = $sheet->getCell("D$row");
            array_push($tableData, [
                // El cliente ahora no se lee desde el excel
                //'idCliente' => $A->getValue(),
                //'numero' => $B->getValue(),
                //'stock' => $C->getValue(),
                // la fecha ahora no se lee desde el excel, se considerea la "fecha de subida" como la fechaStock
                // TODO: EXISTE UN BUG ACA, LA FECHA SE PARSEA MAL, NO ES EXACTAMENTE EL MISMO DIA INDICADO EN EXCEL
                // la fecha por lo general es un (int), con esto se verifica y parsea el el formato correcto si es una fecha
                //'fechaStock' => PHPExcel_Shared_Date::isDateTime($D)?
                //    date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($D->getValue()))
                //    :
                //    $D->getValue(),
                'numero' => $A->getValue(),
                'stock' => $B->getValue(),
            ]);
        }
        return $tableData;
    }
}
