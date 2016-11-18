<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\ActasInventariosFCV;
use App\ArchivoFinalInventario;
use Illuminate\Http\Request;
use Auth;
use File;
// Nominas
use App\Inventarios;

class InformeFinalInventarioFCVController extends Controller {
    public function __construct() {
        // todo: convertir esto a servicio en algun momento....
        // Buscar
        $this->middleware('buscarInventario')
            ->only('show_index_archivoFinal_fcv', 'form_subirZip_fcv', 'api_getActa_fcv',
                'api_actualizarActa_fcv', 'api_publicarActa_fcv', 'api_despublicarActa_fcv');
    }

    /**
     * ##########################################################   VISTAS
     */

    // GET inventarios/informes-finales-fcv
    function show_index_fcv(Request $request){
        // permisos
//        if(!Auth::user()->can('fcv-verInformesFinalesInventario'))
//            return view('errors.403');

        // ultimos 12 meses
        $hoy = Carbon::now();
        $mesesFechaProgramada = \FechaHelper::ultimosMeses($hoy, 7, true);
        $mesesConsolidados = \FechaHelper::ultimosMeses($hoy, 7, true);

        $incluirPendientes = $request->incluirPendientes;
        $cecoBuscado = $request->ceco;
        $mesBuscado = isset($request->mes)? $request->mes : $mesesFechaProgramada[0]['value'];
        $inventarios = Inventarios::buscar((object)[
            'idCliente' => 2, // FCV
            'orden' => 'desc',
            'ceco' => $cecoBuscado,
            'mes' => $mesBuscado,
            'quitarPendientes' => $incluirPendientes!="on"
        ]);

        return view('inventarios.informes-finales.fcv.index', [
            'inventarios' => $inventarios,
            'cecoBuscado' => $cecoBuscado,
            // seleccionar el mes actual por defecto para la busqueda
            'mesBuscado' => $mesBuscado,
            'incluirPendientes' => $incluirPendientes,
            'mesesFechaProgramada' => $mesesFechaProgramada,
            'mesesConsolidados' => $mesesConsolidados,
        ]);
    }

    // GET inventario/{idInventario}/archivo-final-fcv
    // MW: buscarInventario
    function show_index_archivoFinal_fcv(Request $request, $idInventario){
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verInformesFinalesInventario'))
            return view('errors.403');

        // mw: buscarInventario
        $inventario = $request->inventario;

        return view('inventarios.archivo-final.fcv.index', [
            'inventario' => $inventario,
            'acta'=> $inventario->actaFCV,
            'archivos_finales' => $inventario->archivosFinales,
            'puedeEditar' => $user->can('fcv-administrarInformesFinalesInventario')
        ] );
    }

    // POST inventario/{idInventario}/subir-zip-fcv
    // MW: buscarInventario
    function form_subirZip_fcv(Request $request, $idInventario){
        $user = Auth::user();
        if(!$user || !$user->can('fcv-administrarInformesFinalesInventario'))
            return view('errors.403');

        // todo, validar que idInventario corresponda a un inventario de FCV

        // MW: buscarInventario
        $inventario = $request->inventario;
        $local_numero = $inventario->local->numero;

        // se adjunto un archivo?
        if (!$request->hasFile('archivoFinalZip'))
            return redirect()->route('indexArchivoFinalFCV', ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', 'Debe adjuntar el archivo zip.');

        // el archivo es valido?
        $archivo_formulario = $request->file('archivoFinalZip');
        if (!$archivo_formulario->isValid())
            return redirect()->route('indexArchivoFinalFCV', ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', 'El archivo adjuntado no es valido.');

        // mover el archivo a la carpeta correspondiente e insertar en la BD
        $archivoFinalInventario = $inventario->agregarArchivoFinal($user, $archivo_formulario);

        // procesar archivo
        // TODO, CONTINUAR....
        // $resultadoProcesar = $archivoFinalInventario->procesarArchivo();

        // parsear ZIP a un Acta
        $resultadoActa = \ActaInventarioHelper::parsearZIPaActa($archivoFinalInventario->getFullPath(), $local_numero);
        if( isset($resultadoActa->error) ){
            $archivoFinalInventario->setResultado($resultadoActa->error, false);
            return redirect()->route('indexArchivoFinalFCV', ['idInventario'=>$idInventario])
                ->with('mensaje-error-zip', $resultadoActa->error);
        }
        // paso 3) finalmente, actualizar el acta con los datos entregados
        $inventario->insertarOActualizarActa($resultadoActa->acta, $archivoFinalInventario->idArchivoFinalInventario);
        $archivoFinalInventario->setResultado('acta cargada correctamente', true);

        // paso 4) hay datos que no estan en el archivo, o directamente bienen malos, se procesan los archivos "manualmente"
        // para extraer los datos
        // $inventario->actaFCV->leerPatentesInventariadasDesdeElZip(); // este dato biene bien en el txt
        $inventario->actaFCV->leerItemTotalInventarioDesdeElZip();
        $inventario->actaFCV->leerSkuUnicosDesdeElZip();

        // paso 5) dejar el acta como publicada
        $inventario->actaFCV->publicar($user);

        return redirect()->route('indexArchivoFinalFCV', ['idInventario'=>$idInventario])
            ->with('mensaje-exito-zip', $archivoFinalInventario->resultado);
    }

    /**
     * ##########################################################   APIs
     */

    // POST archivo-final-inventario/{idArchivo}/reprocesar-fcv
    function api_reprocesarZip_fcv($idArchivo){
        // validar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-administrarInformesFinalesInventario'))
            return response()->json([], 403);

        $archivoFinalInventario = ArchivoFinalInventario::find($idArchivo);
        $zipPath = $archivoFinalInventario->getFullPath();

        // MW: buscarInventario
        $inventario = $archivoFinalInventario->inventario;
        $local_numero = $inventario->local->numero;

        // procesar archivo
        $resultadoProcesar = $archivoFinalInventario->procesarArchivo();

        // parsear ZIP a un Acta
        $resultadoActa = \ActaInventarioHelper::parsearZIPaActa($zipPath, $local_numero);
        if( isset($resultadoActa->error) )
            return response()->json($resultadoActa->error);

        // paso 3) finalmente, actualizar el acta con los datos entregados
        $inventario->insertarOActualizarActa($resultadoActa->acta, $archivoFinalInventario->idArchivoFinalInventario);

        return response()->json(Inventarios::formatoActa($inventario));
    }

    // GET inventario/{idInventario}/acta-fcv
    // MW: buscarInventario
    function api_getActa_fcv(Request $request, $idInventario){
        // verificar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-verInformesFinalesInventario'))
            return response()->json([], 403);

        // mw: buscarInventario
        $inventario = $request->inventario;
        return response()->json(Inventarios::formatoActa($inventario));
    }

    // POST inventario/{idInventario}/acta-fcv
    // MW: buscarInventario
    function api_actualizarActa_fcv(Request $request, $idInventario){
        // validar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-administrarInformesFinalesInventario'))
            return response()->json([], 403);

        $inventario = $request->inventario;

        $validator = Validator::make($request->all(), [
            // hitos importantes
            'fechaTomaInventario' => 'dateformat:Y-m-d',
            'cliente' => 'string|max:10',
            'ceco' => 'numeric|min:1|max:10000',
            'supervisor' => 'string|max:60',
            'qf' => 'string|max:60',
            'inicioConteo' => 'dateformat:Y-m-d H:i:s',
            'finConteo' => 'dateformat:Y-m-d H:i:s',
            'finProceso' => 'dateformat:Y-m-d H:i:s',
            // dotaciones
            'dotacionPresupuestada' => 'numeric|min:0|max:1000',
            'dotacionEfectiva' => 'numeric|min:0|max:1000',
            // unidades
            'unidadesInventariadas' => 'numeric',
            'unidadesTeoricas' => 'numeric',
            'unidadesDiferenciaNeto' => 'numeric',
            'unidadesDiferenciaAbsoluta' => 'numeric',
            // evaluaciones
            'notaPresentacion' => 'numeric|min:1|max:7',
            'notaSupervisor' => 'numeric|min:1|max:7',
            'notaConteo' => 'numeric|min:1|max:7',
            // consolidado auditoria FCV
            'consolidadoPatentes' => 'numeric',
            'consolidadoUnidades' => 'numeric',
            'consolidadoItems' => 'numeric',
            // Auditoria QF
            'auditoriaQFPatentes' => 'numeric',
            'auditoriaQFUnidades' => 'numeric',
            'auditoriaQFItems' => 'numeric',
            // Auditoria Apoyo 1
            'auditoriaApoyo1Patentes' => 'numeric',
            'auditoriaApoyo1Unidades' => 'numeric',
            'auditoriaApoyo1Items' => 'numeric',
            // Auditoria Apoyo 2
            'auditoriaApoyo2Patentes' => 'numeric',
            'auditoriaApoyo2Unidades' => 'numeric',
            'auditoriaApoyo2Items' => 'numeric',
            // Auditoria Supervisor
            'auditoriaSupervisorPatentes' => 'numeric',
            'auditoriaSupervisorUnidades' => 'numeric',
            'auditoriaSupervisorItems' => 'numeric',
            // Correcciones Auditoria FCV a SEI
            'correccionPatentes' => 'numeric',
            'correccionItems' => 'numeric',
            'correccionUnidadesNeto' => 'numeric',
            'correccionUnidadesAbsolutas' => 'numeric',
            // Porcentaje error auditoria
            'porcentajeErrorSEI' => 'numeric',
            'porcentajeErrorQF' => 'numeric',
            // Variacion Grilla
            'porcentaje_variacion_ajuste_grilla' => 'numeric',
            // Totales Inventario
            'patentesInventariadas' => 'numeric',
            'itemTotalInventariados' => 'numeric',
            'skuUnicosInventariados' => 'numeric',
        ]);
        if($validator->fails()) {
            return response()->json(Inventarios::formatoActa($inventario));
            //return response()->json(['request' => $request->all(), 'errors' => $validator->errors()], 400);
        }else{
            // todo actualizar los datos del acta
            $acta = $inventario->actaFCV;
            // Hitos Importantes
            if(isset($request->fechaTomaInventario))
                $acta->setFechaTomaInventario($request->fechaTomaInventario);
            if(isset($request->cliente))
                $acta->setCliente($request->cliente);
            if(isset($request->ceco))
                $acta->setCeco($request->ceco);
            if(isset($request->supervisor))
                $acta->setSupervisor($request->supervisor);
            if(isset($request->qf))
                $acta->setQF($request->qf);
            if(isset($request->inicioConteo))
                $acta->setInicioConteo($request->inicioConteo);
            if(isset($request->finConteo))
                $acta->setFinConteo($request->finConteo);
            if(isset($request->finProceso))
                $acta->setFinProceso($request->finProceso);
            // dotaciones
            if(isset($request->dotacionPresupuestada))
                $acta->setDotacionPresupuestada($request->dotacionPresupuestada);
            if(isset($request->dotacionEfectiva))
                $acta->setDotacionEfectiva($request->dotacionEfectiva);
            // unidades
            if(isset($request->unidadesInventariadas))
                $acta->setUnidadesInventariadas($request->unidadesInventariadas);
            if(isset($request->unidadesTeoricas))
                $acta->setUnidadesTeoricas($request->unidadesTeoricas);
//            if(isset($request->unidadesDiferenciaNeto))
//                $acta->setDiferenciaNeto($request->unidadesDiferenciaNeto);
            if(isset($request->unidadesDiferenciaAbsoluta))
                $acta->setDiferenciaAbsoluta($request->unidadesDiferenciaAbsoluta);
            // evaluaciones / notas
            if(isset($request->notaPresentacion))
                $acta->setNotaPresentacion($request->notaPresentacion);
            if(isset($request->notaSupervisor))
                $acta->setNotaSupervisor($request->notaSupervisor);
            if(isset($request->notaConteo))
                $acta->setNotaConteo($request->notaConteo);
            // consolidado auditoria FCV
            if(isset($request->consolidadoPatentes))
                $acta->setConsolidadoPatentes($request->consolidadoPatentes);
            if(isset($request->consolidadoUnidades))
                $acta->setConsolidadoUnidades($request->consolidadoUnidades);
            if(isset($request->consolidadoItems))
                $acta->setConsolidadoItems($request->consolidadoItems);
            // Auditoria QF
            if(isset($request->auditoriaQFPatentes))
                $acta->setAuditoriaQF_patentes($request->auditoriaQFPatentes);
            if(isset($request->auditoriaQFUnidades))
               $acta->setAuditoriaQF_unidades($request->auditoriaQFUnidades);
            if(isset($request->auditoriaQFItems))
                $acta->setAuditoriaQF_items($request->auditoriaQFItems);
            // Auditoria Apoyo 1
            if(isset($request->auditoriaApoyo1Patentes))
                $acta->setAuditoriaApoyo1_patentes($request->auditoriaApoyo1Patentes);
            if(isset($request->auditoriaApoyo1Unidades))
                $acta->setAuditoriaApoyo1_unidades($request->auditoriaApoyo1Unidades);
            if(isset($request->auditoriaApoyo1Items))
                $acta->setAuditoriaApoyo1_items($request->auditoriaApoyo1Items);
            // Auditoria Apoyo 2
            if(isset($request->auditoriaApoyo2Patentes))
                $acta->setAuditoriaApoyo2_patentes($request->auditoriaApoyo2Patentes);
            if(isset($request->auditoriaApoyo2Unidades))
                $acta->setAuditoriaApoyo2_unidades($request->auditoriaApoyo2Unidades);
            if(isset($request->auditoriaApoyo2Items))
                $acta->setAuditoriaApoyo2_items($request->auditoriaApoyo2Items);
            // Auditoria Supervisor
            if(isset($request->auditoriaSupervisorPatentes))
                $acta->setAuditoriaSupervisor_patentes($request->auditoriaSupervisorPatentes);
            if(isset($request->auditoriaSupervisorUnidades))
                $acta->setAuditoriaSupervisor_unidades($request->auditoriaSupervisorUnidades);
            if(isset($request->auditoriaSupervisorItems))
                $acta->setAuditoriaSupervisor_items($request->auditoriaSupervisorItems);
            // Correcciones Auditoria FCV a SEI
            if(isset($request->correccionPatentes))
                $acta->setCorreccionPatentesEnAuditoria($request->correccionPatentes);
            if(isset($request->correccionItems))
                $acta->setCorreccionItemsEnAuditoria($request->correccionItems);
            if(isset($request->correccionUnidadesNeto))
                $acta->setCorreccionUnidadesNetoEnAuditoria($request->correccionUnidadesNeto);
            if(isset($request->correccionUnidadesAbsolutas))
                $acta->setCorreccionUnidadesAbsolutasEnAuditoria($request->correccionUnidadesAbsolutas);
            // Porcentaje error auditoria
            //if(isset($request->porcentajeErrorSEI))
            //    $acta->setPorcentajeErrorSEI($request->porcentajeErrorSEI);
            if(isset($request->porcentajeErrorQF))
                $acta->setPorcentajeErrorQF($request->porcentajeErrorQF);
            // Variacion Grilla
            if(isset($request->porcentajeVariacionGrilla))
                $acta->setPorcentajeVariacionGrilla($request->porcentajeVariacionGrilla);
            // Totales Inventario
            if(isset($request->patentesInventariadas))
                $acta->setPatentesInventariadas($request->patentesInventariadas);
            if(isset($request->itemTotalInventariados))
                $acta->setItemTotalInventariados($request->itemTotalInventariados);
            if(isset($request->skuUnicosInventariados))
                $acta->setSkuUnicosInventariados($request->skuUnicosInventariados);


            $inventario = Inventarios::find($idInventario);
            return response()->json(Inventarios::formatoActa($inventario));
        }
    }

    // POSTinventario/{idInventario}/publicar-acta-fcv
    // MW: auth, buscarInventario
    function api_publicarActa_fcv(Request $request, $idInventario){
        // validar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-administrarInformesFinalesInventario'))
            return response()->json([], 403);

        // mw buscarInventario
        $inventario = $request->inventario;

        // publicar, actualizar, y mostrar datos
        $inventario->actaFCV->publicar($user);
        $inventario = Inventarios::find($idInventario);
        return response()->json(Inventarios::formatoActa($inventario));
    }

    // POSTinventario/{idInventario}/despublicar-acta-fcv
    // MW: buscarInventario
    function api_despublicarActa_fcv(Request $request, $idInventario){
        // validar permisos
        $user = Auth::user();
        if(!$user || !$user->can('fcv-administrarInformesFinalesInventario'))
            return response()->json([], 403);

        // mw buscarInventario
        $inventario = $request->inventario;

        // des-publicar, actualizar, y mostrar datos
        $inventario->actaFCV->despublicar();
        $inventario = Inventarios::find($idInventario);
        return response()->json(Inventarios::formatoActa($inventario));
    }

    /**
     * ##########################################################   DESCARGAS
     */

    // GET inventario/archivo-final/{idArchivo}/descargar-fcv
    function descargar_archivoFinal_fcv($idArchivoFinalInventario){
        // solo se descarga si tiene permisos
        if(!Auth::user()->can('fcv-verInformesFinalesInventario'))
            return view('errors.403');

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

    // GET inventarios/descargar-consolidado-fcv
    function descargar_consolidado_fcv(Request $request){
        // solo se descarga si tiene permisos
        if(!Auth::user()->can('fcv-verInformesFinalesInventario'))
            return view('errors.403');

        // todo recibir rango de fecha y otros filtros por el get
        $actas = ActasInventariosFCV::buscar((object)[
            'fechaInicio' => $request->query('fechaInicio'),
            'fechaFin' => $request->query('fechaFin'),
            'mes' => $request->query('mes'),
            'orden' => $request->query('orden'),
        ]);
        $datos = $actas->map(function($acta){
            return [
                // ######  Hitos importantes del proceso de inventario:
                // Fecha Inv
                $acta->getFechaTomaInventario(),
                // CL
                $acta->getCliente(),
                // Loc
                $acta->getCeco(),
                // Supervisor
                $acta->getSupervisor(),
                // Químico
                $acta->getQF(),
                // Inicio Conteo
                $acta->getInicioConteo(true),
                // Fin Conteo
                $acta->getFinConteo(true),
                // Fin Proceso
                $acta->getFinProceso(true),

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
                $acta->getUnidadesInventariadas(),          // ??, no estoy seguro
                // Teórico
                $acta->getUnidadesTeoricas(),
                // Dif. Neto
                $acta->getDiferenciaNeto(),
                // Dif. ABS
                $acta->getDiferenciaAbsoluta(),

                // ######  Evaluaciones / Notas
                // Nota Presentacion
                $acta->getNotaPresentacion(),
                // Nota Supervisor
                $acta->getNotaSupervisor(),
                // Nota Conteo
                $acta->getNotaConteo(),

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
                $acta->getCorreccionPatentesEnAuditoria(),
                // Items
                $acta->getCorreccionItemsEnAuditoria(),
                // Un. Neto
                $acta->getCorreccionUnidadesNetoEnAuditoria(),
                // Un. ABS'
                $acta->getCorreccionUnidadesAbsolutasEnAuditoria(),

                // ######  % Error Aud.
                // SEI
                $acta->getPorcentajeErrorSei(true),
                // QF
                $acta->getPorcentajeErrorQF(true),

                // ######  Variación Grilla
                // %
                $acta->getPorcentajeVariacionGrilla(true),

                // ######  Totales inventario
                // PTT
                $acta->getPatentesInventariadas(false),
                // Items totales
                $acta->getItemTotalInventariados(false),
                // SKU unicos
                $acta->getSkuUnicosInventariados(false),

                // ID Inventario
                //$acta->idInventario
            ];
        })->toArray();

        //return response()->json($datos);
        $workbook = \ExcelHelper::generarWorkbook_consolidadoActas($datos);

        // generar el archivo y descargarlo
        $fullpath = \ExcelHelper::workbook_a_archivo($workbook);
        return \ArchivosHelper::descargarArchivo($fullpath, "consolidado-actas.xlsx");
    }
}