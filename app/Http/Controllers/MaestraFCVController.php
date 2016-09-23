<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
// Carbon
use Carbon\Carbon;
// DB
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
// Modelos
use App\Clientes;
use App\ArchivoMaestraFCV;

class MaestraFCVController extends Controller
{
    private function leerArchivoProductos($inputFileName) {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            return [];
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        //  Loop through each row of the worksheet in turn
        $tableData = [];
        // iniciar el 2, para saltarse el titulo
        for ($row = 2; $row <= $highestRow; $row++){
            array_push($tableData, [
                'a'=> $sheet->getCell("A$row")->getValue(),
                'b'=> $sheet->getCell("B$row")->getValue(),
                'c'=> $sheet->getCell("C$row")->getValue(),
                'd'=> $sheet->getCell("D$row")->getValue(),
                'e'=> $sheet->getCell("E$row")->getValue(),
                'f'=> $sheet->getCell("F$row")->getValue(),
                'g'=> $sheet->getCell("G$row")->getValue(),
                'h'=> $sheet->getCell("H$row")->getValue(),
            ]);
        }
        return $tableData;
    }

    function api_cargar_maestra(){
        ini_set('memory_limit','1024M');
        $tableData = $this->leerArchivoProductos( public_path('/seedFiles/stockMaestraFINAL.xlsx') );

        $almacenDisponible = AlmacenAF::find(1);
        // transaccion, es muy comun tener datos duplicados en las maestras
        DB::transaction(function() use ($tableData, $almacenDisponible){
            // agregar productos
            foreach($tableData as $data) {
                $producto = ProductoAF::find($data['a']);
                $prod = $producto;
                if(!$producto){
                    $prod = new ProductoAF([
                        'SKU'=>$data['a'],
                        'descripcion' => $data['b'],
                        'valorMercado' => $data['f']
                    ]);
                    $prod->save();
                }else{
                    $producto->descripcion = $data['b'];
                    $producto->valorMercado = $data['f'];
                    $producto->save();
                }

                // agregar articulo
                $articulo = new ArticuloAF([
                    'fechaIncorporacion' => Carbon::now(),
                    'stock' => $data['g'],
                    'SKU' => $prod->SKU
                ]);
                $articulo->save();

                // agregar barras
                if(isset($data['c']) && $data['c']!=""){
                    $codigo = new CodigoBarra(['barra'=> $data['c'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }
                if(isset($data['d'])){
                    $codigo = new CodigoBarra(['barra'=> $data['d'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }
                if(isset($data['e'])){
                    $codigo = new CodigoBarra(['barra'=> $data['e'], 'idArticuloAF' => $articulo->idArticuloAF]);
                    $codigo->save();
                }

                // agregar productos a almacen DISPONIBLE
                $almacenDisponible->articulos()->attach( $articulo->idArticuloAF, [
                        'stockActual' => $data['g']
                    ]
                );
            }
        });
        return response()->json(
            collect($tableData)->count()
        );
    }

    function subir_maestra(Request $request){
        ini_set('memory_limit','1024M');
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $fileName = "$originalName";
        //dd($fileName);
        $file->move( public_path().'/actualizarStock/', $fileName);
        chmod(public_path().'/actualizarStock/'.$fileName, 0774);
        /*try{
            $datos = $this->leerArchivo(public_path().'/actualizarStock/'.$fileName);
        } catch (ErrorException $e) {
            return response()->json(['error'=>'Error al procesar el archivo']);
        }
        $data = collect($datos);
        //Obtener fecha actual maestra
        $fechaActual = Carbon::now()->format("Y-m-d");
        return $this->insertMaestra();*/
    }
    
    public function show_maestra_producto(){
        $maestraFCV = ArchivoMaestraFCV::all();
        return view('operacional.maestra.maestra-producto', ['maestras' => $maestraFCV]);
    }
    
    public function download_Maestra($idArchivoFinalInventario){
        $archivo = ArchivoFinalInventario::find($idArchivoFinalInventario);
        if(!$archivo)
            return view('errors.errorConMensaje', [
                'titulo' => 'Archivo No encontrado', 'descripcion' => 'El archivo que busca no se puede descargar.'
            ]);
        $download_archivo = $archivo->nombre_archivo;
        $file= public_path(). "/FSB/archivoFinalInventario/". "$download_archivo";
        $headers = array(
            'Content-Type: application/octet-stream',
        );
        return Response::download($file, $download_archivo, $headers);
    }
}
