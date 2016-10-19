<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => ['web']], function (){
    Route::get('/',             'HomeController@index');
    /*
    |--------------------------------------------------------------------------
    | Autorización y Autenticación
    |--------------------------------------------------------------------------
    |*/
    Route::auth();
    /*
    |--------------------------------------------------------------------------
    | RUTAS PROTEGIDAS SOLO A USUARIOS
    |--------------------------------------------------------------------------
    |*/
    Route::group(['middleware'=>['auth']], function(){
        // ################ CLIENTES Y LOCALES
        // VISTAS:
        Route::get('admin/clientes',                                'ClientesController@show_Lista')->name('admin.clientes.lista');
        Route::get('admin/locales',                                 'LocalesController@show_mantenedor')->name('admin.locales.lista');
        Route::get('admin/stock',                                   'StockController@show_mantenedorStock');
        // API:
        Route::get('api/clientes',                                  'ClientesController@api_getClientes');
        Route::get('api/cliente/{idCliente}/locales',               'LocalesController@api_getLocales');
        Route::post('api/locales',                                  'LocalesController@api_nuevo');
        Route::put('api/local/{idLocal}',                           'LocalesController@api_actualizar');


        // ################ USUARIOS, ROLES y PERMISOS
        // VISTAS:
        Route::get('admin/usuarios-roles',                          'AuthController@show_usuarios_roles');
        Route::get('admin/permissions-roles',                       'AuthController@show_permissions_roles');
        Route::get('admin/permissions',                             'AuthController@show_permissions');
        Route::get('admin/roles',                                   'AuthController@show_roles');
        Route::get('personal',                                      'PersonalController@show_personal_index');
        // cambio de contraseña
        Route::get('user/changePassword',                           'AuthController@show_changePassword');
        Route::post('user/changePassword',                          'AuthController@post_change_password');
        // APIS:
        // usuarios
        Route::get('api/usuario/{idUsuario}',                       'PersonalController@api_usuario_get');
        Route::put('api/usuario/{idUsuario}',                       'PersonalController@api_usuario_actualizar');
        Route::get('api/usuario/{rut}/historial-nominas',           'PersonalController@api_historial_nominas'); /*DESARROLLO*/
        Route::get('api/usuarios/buscar',                           'PersonalController@api_usuarios_buscar');
        Route::post('api/usuarios/nuevo-operador',                  'PersonalController@api_operador_nuevo');
        // permisos
        Route::post('api/permission/nuevo',                         'AuthController@api_permission_nuevo');
        Route::put('api/permission/{idPermission}/editar',          'AuthController@api_permission_actualizar');
        Route::delete('api/permission/{idPermission}',              'AuthController@api_permission_eliminar');
        // roles
        Route::post('api/roles',                                    'AuthController@api_role_nuevo');
        Route::put('api/role/{idRole}',                             'AuthController@api_role_actualizar');
        Route::delete('api/role/{idRole}',                          'AuthController@api_eliminarRole');
        // usuarios - roles
        Route::post('api/usuario/{idUsuario}/role/{idRole}',        'AuthController@api_nuevo_rol');
        Route::delete('api/usuario/{idUsuario}/role/{idRole}',      'AuthController@api_delete_rol');
        // roles - permisos
        Route::post('api/permission/{idPermission}/role/{idRole}',  'AuthController@api_nuevo_permiso');
        Route::delete('api/permission/{idPermission}/roles/{idRole}','AuthController@api_delete_permiso');


        // ################ INVENTARIOS
        // VISTAS:
        Route::get('programacionIG',                                'InventariosController@showProgramacionIndex');
        Route::get('programacionIG/mensual',                        'InventariosController@showProgramacionMensual');
        Route::get('programacionIG/semanal',                        'InventariosController@showProgramacionSemanal');
        // API:
        Route::get('api/inventarios/buscar-2',                      'InventariosController@api_buscar_2');
        Route::post('api/inventario/nuevo',                         'InventariosController@api_nuevo');
        Route::get('api/inventario/{idInventario}',                 'InventariosController@api_get');
        Route::put('api/inventario/{idInventario}',                 'InventariosController@api_actualizar');
        Route::delete('api/inventario/{idInventario}',              'InventariosController@api_eliminar');
        // DESCARGAS:
        Route::get('/pdf/inventarios/{mes}/cliente/{idCliente}',                            'Legacy_InventariosController@descargarPDF_porMes');
        Route::get('/pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',   'Legacy_InventariosController@descargarPDF_porRango');

        // ################ NOMINAS
        // VISTAS:
        Route::get('nominas/captadores',                            'NominasController@show_captadores');
        Route::get('programacionIG/nomina/{idNomina}',              'NominasController@show_nomina');
        // API:
        Route::put('api/nomina/{idNomina}',                         'NominasController@api_actualizar');
        Route::get('api/nominas/buscar',                            'NominasController@api_buscar');
        // api para cambios en la dotacion de las nominas
        Route::get('api/nomina/{idNomina}/dotacion',                'NominasController@api_get');
        Route::get('api/nomina/{idNomina}/lideres-disponibles',     'NominasController@api_lideresDisponibles');
        Route::post('api/nomina/{idNomina}/lider/{usuarioRUN}',     'NominasController@api_agregarLider');
        Route::delete('api/nomina/{idNomina}/lider',                'NominasController@api_quitarLider');
        Route::post('api/nomina/{idNomina}/supervisor/{usuarioRUN}','NominasController@api_agregarSupervisor');
        Route::delete('api/nomina/{idNomina}/supervisor',           'NominasController@api_quitarSupervisor');
        Route::post('api/nomina/{idNomina}/operador/{usuarioRUN}',  'NominasController@api_agregarOperador');
        Route::delete('api/nomina/{idNomina}/operador/{usuarioRUN}','NominasController@api_quitarOperador');
        Route::post('api/nomina/{idNomina}/captador/{idUsuario}',   'NominasController@api_agregarCaptador');
        Route::delete('api/nomina/{idNomina}/captador/{idUsuario}', 'NominasController@api_quitarCaptador');
        Route::put('api/nomina/{idNomina}/captador/{idUsuario}',    'NominasController@api_cambiarAsignadosDeCaptador');
        Route::post('api/nomina/{idNomina}/estado-enviar',          'NominasController@api_enviarNomina');
        Route::post('api/nomina/{idNomina}/estado-aprobar',         'NominasController@api_aprobarNomina');
        Route::post('api/nomina/{idNomina}/estado-rechazar',        'NominasController@api_rechazarNomina');
        Route::post('api/nomina/{idNomina}/estado-informar',        'NominasController@api_informarNomina');
        Route::post('api/nomina/{idNomina}/estado-rectificar',      'NominasController@api_rectificarNomina');

        // ################ ARCHIVO FINAL DE INVENTARIO / ACTAS
        // VISTAS:
        Route::get('informes-finales-inventarios-fcv',                  'ArchivoFinalInventarioController@show_informes_finales_inventario_fcv');
        Route::get('inventario/{idInventario}/archivo-final',           'ArchivoFinalInventarioController@show_archivofinal_index')->name('indexArchivoFinal');
        Route::post('inventario/{idInventario}/subir-zip-fcv',          'ArchivoFinalInventarioController@api_subirZipFCV');
        // DESCARGAS:
        Route::get('inventario/descargar-consolidado-fcv',              'ArchivoFinalInventarioController@descargar_consolidado_fcv');
        Route::get('archivo-final-inventario/{idArchivo}/descargar',    'ArchivoFinalInventarioController@descargar_archivo_final');
        // APIS:
        Route::get('api/inventario/{idInventario}/acta',                'ArchivoFinalInventarioController@api_getActa');
        Route::post('api/inventario/{idInventario}/acta',               'ArchivoFinalInventarioController@api_actualizarActa');
        Route::post('api/inventario/{idInventario}/publicar-acta',      'ArchivoFinalInventarioController@api_publicarActa');
        Route::post('api/inventario/{idInventario}/despublicar-acta',   'ArchivoFinalInventarioController@api_despublicarActa');
        Route::post('api/archivo-final-inventario/{idArchivo}/reprocesar',   'ArchivoFinalInventarioController@api_reprocesar_zip');


        // ################ AUDITORIAS
        // VISTAS:
        // programacion auditorias
        Route::get('auditorias/estado-general-fcv',                     'AuditoriasController@show_estado_general_fcv');
        Route::get('programacionAI/mensual',                            'AuditoriasController@showMensual');
        Route::get('programacionAI/semanal',                            'AuditoriasController@showSemanal');
        // DESCARGAS:
        Route::get('pdf/auditorias/{mes}/cliente/{idCliente}',          'Legacy_AuditoriasController@descargarPDF_porMes');
        Route::get('pdf/auditorias/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',     'Legacy_AuditoriasController@descargarPDF_porRango');
        // API:
        Route::post('api/auditoria/nuevo',                              'AuditoriasController@api_nuevo');
        Route::put('api/auditoria/{idAuditoria}',                       'AuditoriasController@api_actualizar');
        Route::delete('api/auditoria/{idAuditoria}',                    'AuditoriasController@api_eliminar');
        Route::get('api/auditoria/buscar',                              'AuditoriasController@api_buscar');

        // ################ MAESTRA DE PRODUCTOS
        // VISTAS:
        Route::get('maestra-productos-fcv',                             'MaestraProductosController@show_maestra_productos_fcv')->name('maestraFCV');
        Route::post('maestra-productos/subir-maestra-fcv',              'MaestraProductosController@subir_maestraFCV');
        // DESCARGAS:
        Route::get('maestra-productos/{idArchivoMaestra}/descargar',    'MaestraProductosController@descargar_archivo');

        // ################ MUESTRAS DE VENCIMIENTO
        // VISTAS:
        Route::get('muestra-vencimiento-fcv',                           'MuestraVencimientoController@show_indexFCV')->name('indexMuestraVencimientoFCV');
        Route::post('muestra-vencimiento-fcv/subir-muestra-fcv',        'MuestraVencimientoController@api_subirMuestraFCV');
        // DESCARGAS:
        Route::get('muestra-vencimiento-fcv/{idMuestra}/descargar',     'MuestraVencimientoController@descargar_muestraFCV');

        // ################ ARCHIVO FINAL AUDITORIA
        // DESCARGAS:
        Route::get('api/archivo-final-auditoria/{idArchivoCruzVerde}/descargar-zip',  'ArchivoFinalAuditoriaController@api_descargarZIP');


        // ################ VISTA GENERAL INVENTARIOS-AUDITORIAS
        // API:
        Route::get('api/vista-general/nominas-inventarios',             'VistaGeneralController@api_obtenerNominasAuditorias');


        // ################ ACTIVOS FIJOS
        // VISTAS:
        Route::get('activo-fijo',                                   'ActivosFijosController@get_index');
        // API:
        // productos
        Route::get('api/activo-fijo/productos/buscar',              'ActivosFijosController@api_productos_buscar');
        Route::post('api/activo-fijo/productos/nuevo',              'ActivosFijosController@api_productos_nuevo');
        Route::put('api/activo-fijo/producto/{sku}',                'ActivosFijosController@api_producto_actualizar');
        Route::delete('api/activo-fijo/producto/{sku}',             'ActivosFijosController@api_producto_eliminar');
        // articulos
        Route::get('api/activo-fijo/articulos/buscar',              'ActivosFijosController@api_articulos_buscar');
        Route::post('api/activo-fijo/articulos/entregar',           'ActivosFijosController@api_articulos_entregar_a_almacen');
        Route::post('api/activo-fijo/articulos/transferir',         'ActivosFijosController@api_articulos_transferir');
        Route::post('api/activo-fijo/articulos/nuevo',              'ActivosFijosController@api_articulos_nuevo');
        Route::put('api/activo-fijo/articulo/{idArticuloAF}',       'ActivosFijosController@api_articulo_actualizar');
        Route::delete('api/activo-fijo/articulo/{idArticuloAF}',    'ActivosFijosController@api_articulo_eliminar');
        // barra
        Route::post('api/activo-fijo/barras/nuevo',                 'ActivosFijosController@api_barra_nueva');
        Route::delete('api/activo-fijo/barra/{codBarra}',           'ActivosFijosController@api_barra_eliminar');
        // almacenes
        Route::get('api/activo-fijo/almacenes/buscar',              'ActivosFijosController@api_almacenes_buscar');
        Route::post('api/activo-fijo/almacen/nuevo',                'ActivosFijosController@api_almacen_nuevo');
        Route::get('api/activo-fijo/almacen/{idAlmacen}/articulos', 'ActivosFijosController@api_almacen_articulos');
        // preguias
        Route::get('api/activo-fijo/preguias/buscar',               'ActivosFijosController@api_preguias_buscar');
        Route::get('api/activo-fijo/preguia/{idPreguia}',           'ActivosFijosController@api_preguia_fetch');
        Route::post('api/activo-fijo/preguia/{idPreguia}/devolver', 'ActivosFijosController@api_preguia_devolver');
        // responsables de almacenes
        Route::get('api/activo-fijo/responsables/buscar',           'ActivosFijosController@api_responsables_buscar');

        // ################ OTROS
        Route::get('api/comunas',                                   'OtrosController@api_comunas');
        Route::post('api/stock/upload',                             'StockController@api_uploadArchivo');
        Route::post('api/stock/pegar',                              'StockController@api_pegarDatos');
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS PUBLICAS
    |--------------------------------------------------------------------------
    |*/
    Route::group([], function(){
        // ####     USUARIOS
        // API:
        Route::get('api/usuario/{idUsuario}/roles',                 'PersonalController@api_getRolesUsuario');
        Route::get('api/usuarios/descargar-excel',                  'PersonalController@excel_descargarTodos');

        // ####     INVENTARIOS
        // VISTAS:
        Route::get('programacionIG/nomina/{publicIdNomina}/pdf',    'NominasController@show_nomina_pdfDownload');
        Route::get('programacionIG/nomina/{publicIdNomina}/excel',  'NominasController@show_nomina_excelDownload');
        Route::get('programacionIG/nomina/{idNomina}/pdf-preview',  'NominasController@show_nomina_pdfPreview');
        // API:
        Route::get('api/inventarios/buscar',                         'Legacy_InventariosController@api_publica_buscar');
        Route::post('api/inventarios/informar-archivo-final',        'Legacy_InventariosController@api_publica_informarArchivoFinal');

        // ####     NOMINAS
        // API:
        Route::post('api/nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible', 'NominasController@api_informarDisponible');
        Route::post('api/nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-nomina-pago','NominasController@api_informarNominaPago');

        // ####     AUDITORIAS
        // VISTAS:
        Route::get('auditorias/estado-general-fcv-publico',         'AuditoriasController@show_estado_general_fcv_publico');
        // API:
        Route::get('api/auditoria/{fecha1}/al/{fecha2}/auditor/{idAuditor}',        'Legacy_AuditoriasController@api_getPorRangoYAuditor');
        Route::post('api/auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado',   'AuditoriasController@api_informarRealizado');
        Route::post('api/auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-revisado',    'AuditoriasController@api_informarRevisado');
        Route::post('api/auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-fecha',       'AuditoriasController@api_informarFecha');

        // Rutas temporales, para testing, pruebas, y emergencias
        Route::get('subir',                                         'TemporalController@show_index');
        Route::get('descargar-otro/{file}',                         'TemporalController@descargar_otro');
        Route::post('completado',                                   'TemporalController@post_archivo');
        Route::get('usuarioComoOperador/{runUsuario}',              'TemporalController@usuarioComoOperador');

        // ruta temporal, es utilizada por la plataforma inventario para descargar el excel generado manualmente
        Route::get('archivo-final-inventario/excel-actas',          'ArchivoFinalInventarioController@temp_descargarExcelActas');
    });
});