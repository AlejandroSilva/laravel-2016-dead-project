<?php

namespace App\Http\Controllers;

use App\ActasInventariosFCV;
use App\ArchivoFinalInventario;
use Illuminate\Http\Request;
use Auth;
use File;
// Nominas
use App\Inventarios;

class ArchivoFinalInventarioController extends Controller {

    // GET inventario/descargar-consolidado-fcv
    function descargar_consolidado_fcv(Request $request){
        // todo recibir rango de fecha y otros filtros por el get
        $actas = ActasInventariosFCV::buscar();

        $cabeceras = [
            'fecha inventario',
            'cliente',
            'ceco',
            'supervisor',
            'químico farmacéutico',
            'nota presentación',
            'nota supervisor',
            'nota conteo',
            'inicio conteo',
            'fin conteo',
            'fin revisión',
            'horas trabajadas',
            'dotacion presupuestada',
            'dotacion efectiva',
            'unidades inventariadas',
            'unidades teóricas',
            'unidades ajustadas (Valor Absoluto)',
            // patentes
            'PTT Total Inventariadas',
            'PTT Revisadas Totales',
            'PTT Revisadas QF',
            'PTT Revisadas apoyo FCV 1',
            'PTT Revisadas apoyo FCV 2',
            'PTT Revisadas Supervisores FCV',
            // items y SKUs
            'Total SKU inventariados',
            'Total items inventariados',
            'Total items cod interno',
            'Items auditados',
            'Items revisados QF',
            'Items revisados apoyo CV 1',
            'Items revisados apoyo CV 2',
            'SKU auditados',
            'Unidades corregidas en revisión previo ajuste',
            'Unidades corregidas',
        ];
        $datos = $actas->map(function($acta){
            return [
                $acta->fecha_toma,
                $acta->nombre_empresa,
                $acta->cod_local,
                $acta->usuario,
                $acta->administrador,
                $acta->nota1,
                $acta->nota2,
                $acta->nota3,
                $acta->captura_uno,
                $acta->fin_captura,
                $acta->fecha_revision_grilla,
                $acta->getHorasTrabajadas(),
                $acta->presupuesto,
                $acta->efectiva,
                $acta->unidades, // getUnidadesInventariadasF()
                $acta->teorico_unidades, // getUnidadesTeoricas()
                $acta->unid_absoluto_corregido_auditoria,
                // patentes
                $acta->ptt_inventariadas,
                $acta->aud1,
                $acta->ptt_rev_qf,
                $acta->ptt_rev_apoyo1,
                $acta->ptt_rev_apoyo2,
                $acta->ptt_rev_supervisor_fcv,
                // items y SKUs
                '-',
                $acta->total_items_inventariados,   // tot3 || total_items_inventariados
                $acta->total_items_inventariados ,
                $acta->aud2,
                $acta->items_rev_qf,
                $acta->items_rev_apoyo1,
                $acta->items_rev_apoyo2,
                '-',
                '-',
                $acta->unid_absoluto_corregido_auditoria,
            ];
        })->toArray();

        $workbook = \ExcelHelper::generarWorkbook($cabeceras, $datos);

        // generar el archivo y descargarlo
        $fullpath = \ExcelHelper::workbook_a_archivo($workbook);
        return \ArchivosHelper::descargarArchivo($fullpath, "consolidado-actas.xlsx");
    }

    // GET inventario/{idInventario}/archivo-final
    function show_archivofinal_index($idInventario){
        // existe el inventario?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return view('errors.errorConMensaje', [
                'titulo' => 'Inventario no encontrado', 'descripcion' => 'El inventario que busca no ha sido encontrado.'
            ]);

        return view('archivo-final-inventario.archivo-final-index', [
            'inventario' => $inventario,
            'acta'=> $inventario->actaFCV,
            'archivos_finales' => $inventario->archivosFinales
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

    // POSTinventario/{idInventario}/publicar-acta
    function api_publicarActa($idInventario){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaAuditorias_ver'))
            return view('errors.403');

        // el inventario existe?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return view('errors.errorConMensaje', [
                'titulo' => 'Inventario no encontrado', 'descripcion' => 'El inventario que busca no ha sido encontrado.'
            ]);

        // publicar
        $inventario->actaFCV->publicar($user);
        return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario]);
    }

    // POSTinventario/{idInventario}/despublicar-acta
    function api_despublicarActa($idInventario){
        // validar de que el usuario tenga los permisos
        $user = Auth::user();
        if(!$user || !$user->can('programaAuditorias_ver'))
            return view('errors.403');

        // el inventario existe?
        $inventario = Inventarios::find($idInventario);
        if(!$inventario)
            return view('errors.errorConMensaje', [
                'titulo' => 'Inventario no encontrado', 'descripcion' => 'El inventario que busca no ha sido encontrado.'
            ]);

        // publicar
        $inventario->actaFCV->despublicar();
        return redirect()->route("indexArchivoFinal", ['idInventario'=>$idInventario]);
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