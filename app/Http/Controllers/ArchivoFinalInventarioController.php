<?php

namespace App\Http\Controllers;

use App\ArchivoFinalInventario;
use Illuminate\Http\Request;
use Auth;
use File;
// Nominas
use App\Inventarios;

class ArchivoFinalInventarioController extends Controller {

    // GET inventario/{idInventario}/archivo-final
    function show_archivofinal_index($idInventario){
        // existe el inventario?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return view('errors.errorConMensaje', [
                'titulo' => 'Inventario no encontrado', 'descripcion' => 'El inventario que busca no ha sido encontrado.'
            ]);

        $acta = $inventario->actaInventarioFCV;

        return view('archivo-final-inventario.archivo-final-index', [
            'acta'=>$acta,
            'archivos_finales' => $inventario->archivosFinales,
            'idInventario' => $inventario->idInventario
        ] );
    }

    // POST inventario/{idInventario}/subir-zip-fcv
    function api_subirZipFCV(Request $request, $idInventario){
        // todo validar permisos
        // todo, validar que sea de FCV el archivo

        // nomina existe?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', 'El inventario indicado no existe');
        $local_numero = $inventario->local->numero;

        // se adjunto un archivo?
        if (!$request->hasFile('archivoFinalZip'))
            return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', 'Debe adjuntar el archivo zip.');

        // el archivo es valido?
        $archivo_formulario = $request->file('archivoFinalZip');
        if (!$archivo_formulario->isValid())
            return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', 'El archivo adjuntado no es valido.');

        // mover el archivo a la carpeta correspondiente e insertar en la BD
        $archivoFinalInventario = $inventario->agregarArchivoFinal(Auth::user(), $archivo_formulario);

        // parsear ZIP a un Acta
        $resultadoActa = \ActaInventarioHelper::parsearZIPaActa($archivoFinalInventario->getFullPath(), $local_numero);
        if( isset($resultadoActa->error) ){
            $archivoFinalInventario->setResultado($resultadoActa->error, false);
            return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', $resultadoActa->error);
        }

        // paso 3) finalmente, actualizar el acta con los datos entregados
        $inventario->insertarOActualizarActa($resultadoActa->acta, $archivoFinalInventario->idArchivoFinalInventario);
        $archivoFinalInventario->setResultado('acta cargada correctamente', true);

        return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario])
            ->with('mensaje-exito-zip', $archivoFinalInventario->resultado);
        //return response()->json($resultadoActa->acta);
    }

    // GET archivo-final-inventario/{idArchivo}/descargar
    public function descargar_archivo_final($idArchivoFinalInventario){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('admin-maestra-fcv'))
            return response()->view('errors.403', [], 403);

        // existe el registro en la BD?
        $archivoFinalInventario = ArchivoFinalInventario::find($idArchivoFinalInventario);
        if(!$archivoFinalInventario)
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo final no encontrado',
                'descripcion' => 'No hay registros del archivo final que busca. Contactese con el departamento de informática.',
            ]);

        $fullPath = $archivoFinalInventario->getFullPath();
        $nombreOriginal = $archivoFinalInventario->nombre_original;
        return \ArchivosHelper::descargarArchivo($fullPath, $nombreOriginal);
    }

    // GET archivo-final-inventario/excel-actas
    function temp_descargarExcelActas(){
        // archivo con ruta fija
        $fullPath = public_path()."/actas-septiembre-2016.xlsx";
        $nombreOriginal = "actas-septiembre-2016.xlsx";

        // existe el archivo fisicamente en el servidor?
        if(!File::exists($fullPath))
            return response()->view('errors.errorConMensaje', [
                'titulo' =>  'Archivo no encontrado',
                'descripcion' => 'El archivo que busca no ha sido encontrado. Contactese con el departamento de informática.',
            ]);

        return response()
            ->download($fullPath, $nombreOriginal, [
                'Content-Type'=>'application/force-download',   // forzar la descarga en Opera Mini
                'Pragma'=>'no-cache',
                'Cache-Control'=>'no-cache, must-revalidate'
            ]);
    }
}