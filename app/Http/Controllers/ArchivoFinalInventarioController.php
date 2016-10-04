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
        $datos = $actas->map(function($acta){
            return [
                // ######  Hitos importantes del proceso de inventario:
                // Fecha Inv
                $acta->fecha_toma,
                // CL
                $acta->nombre_empresa,
                // Loc
                $acta->cod_local,
                // Supervisor
                $acta->usuario,
                // Químico
                $acta->administrador,
                // Inicio Conteo
                $acta->getInicioConteo(),
                // Fin Conteo
                $acta->getFinConteo(),
                // Fin Proceso
                $acta->getFinProceso(),

                // ######  Duración
                // Conteo
                $acta->getDuracionConteo(true),
                // Revisión
                $acta->getDuracionRevision(true),
                // Total Proceso
                $acta->getDuracionTotalProceso(true),

                // ######  Dotaciones
                // Ppto.
                $acta->getDotacionPresupuestada(false),
                // Efectivo
                $acta->getDotacionEfectiva(false),

                // ######  unidades
                // Conteo
                $acta->getUnidadesInventariadas(false),          // ??, no estoy seguro
                // Teórico
                $acta->getUnidadesTeoricas(),
                // Dif. Neto
                $acta->getDiferenciaNeto(false),
                // Dif. ABS
                $acta->getDiferenciaAbsoluta(false),

                // ######  Evaluaciones / Notas
                // Nota Presentacion
                $acta->getNotaPresentacion(false),
                // Nota Supervisor
                $acta->getNotaSupervisor(false),
                // Nota Conteo
                $acta->getNotaConteo(false),

                // ######  Consolidado Auditoria FCV
                // Patente
                $acta->getConsolidadoPatentes(false),
                // Unidades
                $acta->getConsolidadoUnidades(false),
                // Items
                $acta->getConsolidadoItems(false),

                // ######  Auditoria QF
                // Patente
                $acta->getAuditoriaQF_patentes(false),
                // Unidades
                $acta->getAuditoriaQF_unidades(false),
                // Items
                $acta->getAuditoriaQF_items(false),

                // ######  Auditoria Apoyo 1
                // Patente
                $acta->getAuditoriaApoyo1_patentes(false),
                // Unidades
                $acta->getAuditoriaApoyo1_unidades(false),
                // Items
                $acta->getAuditoriaApoyo1_items(false),

                // ######  Auditoria Apoyo 2
                // Patente
                $acta->getAuditoriaApoyo2_patentes(false),
                // Unidades
                $acta->getAuditoriaApoyo2_unidades(false),
                // Items
                $acta->getAuditoriaApoyo2_items(false),

                // ######  Auditoria Supervisor
                // Patente
                $acta->getAuditoriaSupervisor_patentes(false),
                // Unidades
                $acta->getAuditoriaSupervisor_unidades(false),
                // Items
                $acta->getAuditoriaSupervisor_items(false),

                // ######  Correciones Auditoria FCV a SEI
                // Patentes
                $acta->getCorreccionPatentesEnAuditoria(false),
                // Items
                $acta->getCorreccionItemsEnAuditoria(false),
                // Un. Neto
                $acta->getCorreccionUnidadesNetoEnAuditoria(false),
                // Un. ABS'
                $acta->getCorreccionUnidadesAbsolutasEnAuditoria(false),

                // ######  % Error Aud.
                // SEI
                $acta->getPorcentajeErrorSei(true),
                // QF
                $acta->getPorcentajeErrorQF(true),

                // ######  Variación Grilla
                // %
                $acta->getPorcentajeVariacionGrilla(true),
                // SKU Inv
                $acta->getSKUInventariados(true),
            ];
        })->toArray();

        $workbook = \ExcelHelper::generarWorkbook_consolidadoActas($datos);

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