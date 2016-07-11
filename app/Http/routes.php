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
    | VISTAS PROTEGIDAS SOLO A USUARIOS
    |--------------------------------------------------------------------------
    |*/
    Route::group(['middleware'=>['auth']], function(){
        // MANTENEDOR CLIENTES Y LOCALES
        Route::get('admin/clientes',        'ClientesController@show_Lista')->name('admin.clientes.lista');
        Route::get('admin/locales',         'LocalesController@show_mantenedor')->name('admin.locales.lista');
        Route::get('admin/stock',           'StockController@show_mantenedorStock');
        // USUARIOS -MANTENEDOR USUARIOS-ROLES
        Route::get('admin/usuarios-roles',                          'PersonalController@showUsuariosRoles');
        // PERMISSIONS- MANTENEDOR PERMISSIONS-ROLES
        Route::get('admin/permissions-roles',                       'PersonalController@showPermissionsRoles');
        // MANTENEDOR PERMISSIONS
        Route::get('admin/permissions',                              'PersonalController@showPermissions');
        // MANTENEDOR ROLES
        Route::get('admin/roles',                                    'PersonalController@showRoles');
        // CAMBIO DE CONTRASEÑA
        Route::get('user/changePassword',                            'PersonalController@showChangePassword');
        Route::post('user/changePassword',                           'PersonalController@postChangePassword');
        // INVENTARIOS - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('inventario',                  'InventariosController@showIndex');
//        Route::get('inventario/nuevo',            'InventariosController@showNuevo');
//        Route::get('inventario/lista',            'InventariosController@showLista');
        // INVENTARIOS - PROGRAMACION IG
        Route::get('programacionIG',                  'InventariosController@showProgramacionIndex');
        Route::get('programacionIG/mensual',          'InventariosController@showProgramacionMensual');
        Route::get('programacionIG/semanal',          'InventariosController@showProgramacionSemanal');
        // INVENTARIOS - DESCARGA DE PDF
        Route::get('/pdf/inventarios/{mes}/cliente/{idCliente}',    'InventariosController@descargarPDF_porMes');
        Route::get('/pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',   'InventariosController@descargarPDF_porRango');
        // INVENTARIO - MANTENEDOR DE NOMINAS
        Route::get('programacionIG/nomina/{idNomina}',              'NominasController@show_nomina');
        // NOMINAS - NOMINAS DE CAPTADOR
        Route::get('nominas/captadores',                            'NominasController@show_captadores');
        Route::get('nominas/captador/{idCaptador}',                 'NominasController@show_nominasCaptador');
        // AUDITORIAS - PROGRAMACION AI
        Route::get('programacionAI',                 'AuditoriasController@showProgramacionIndex');
        Route::get('programacionAI/mensual',          'AuditoriasController@showMensual');
        Route::get('programacionAI/semanal',          'AuditoriasController@showSemanal');
        // AUDITORIAS - DESCARGA DE PDF
        Route::get('pdf/auditorias/{mes}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porMes');
        Route::get('pdf/auditorias/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porRango');

        // ACTIVOS FIJOS
        Route::get('activo-fijo',             'ActivosFijosController@get_index');
        // api (ordenar luego)

        Route::get('api/activo-fijo/cargar-productos',                      'MaestraController@api_cargar_productos');  // ELIMINAR
        Route::get('api/activo-fijo/cargar-articulos',                      'MaestraController@api_cargar_articulos');  // ELIMINAR

        // USUARIOS - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('personal/nuevo',             'PersonalController@show_formulario')->name('personal.nuevo');
        // GEO - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('geo',             'GeoController@show_index');//->name('geo.index');
    });
    /*
    |--------------------------------------------------------------------------
    | VISTAS PUBLICAS
    |--------------------------------------------------------------------------
    |*/
    Route::group([], function(){
        Route::get('programacionIG/nomina/{publicIdNomina}/pdf',    'NominasController@show_nomina_pdfDownload');
        Route::get('programacionIG/nomina/{publicIdNomina}/excel',  'NominasController@show_nomina_excelDownload');
        Route::get('programacionIG/nomina/{idNomina}/pdf-preview',  'NominasController@show_nomina_pdfPreview');
    });
    
    /*
    |--------------------------------------------------------------------------
    | API's PROTEGIDAS SOLO A USUARIOS
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix'=>'api',  'middleware'=>['authAPI']], function() {
        // API CLIENTES Y LOCALES
        Route::get('clientes',                      'ClientesController@api_getClientes');
        Route::get('cliente/{idCliente}/locales',   'LocalesController@api_getLocales');
        Route::post('locales',                      'LocalesController@api_nuevo');
        Route::put('local/{idLocal}',             'LocalesController@api_actualizar');
//        Route::get('locales/{idLocal}/verbose',     'LocalesController@api_getLocalVerbose');
        // API INVENTARIOS
        Route::post('inventario/nuevo',                 'InventariosController@api_nuevo');
        Route::get('inventario/{idInventario}',         'InventariosController@api_get');
        Route::put('inventario/{idInventario}',         'InventariosController@api_actualizar');
        Route::delete('inventario/{idInventario}',      'InventariosController@api_eliminar');
        // API DE NOMINAS
        Route::get('nominas/buscar',                                'NominasController@api_buscar');
        Route::put('nomina/{idNomina}',                             'NominasController@api_actualizar');
        // -- cambios en la dotacion de las nominas
        Route::get('nomina/{idNomina}/dotacion',                    'NominasController@api_get');
        Route::post('nomina/{idNomina}/lider/{usuarioRUN}',         'NominasController@api_agregarLider');
        Route::delete('nomina/{idNomina}/lider',                    'NominasController@api_quitarLider');
        Route::post('nomina/{idNomina}/supervisor/{usuarioRUN}',    'NominasController@api_agregarSupervisor');
        Route::delete('nomina/{idNomina}/supervisor',               'NominasController@api_quitarSupervisor');
        Route::post('nomina/{idNomina}/operador/{usuarioRUN}',      'NominasController@api_agregarOperador');
        Route::delete('nomina/{idNomina}/operador/{usuarioRUN}',    'NominasController@api_quitarOperador');
//        Route::put('nomina/{idNomina}/operador/{operadorRUN}',      'NominasController@api_modificarOperador');
        // -- cambio en los estados de las nominas
        Route::post('nomina/{idNomina}/estado-enviar',              'NominasController@api_enviarNomina');
        Route::post('nomina/{idNomina}/estado-aprobar',             'NominasController@api_aprobarNomina');
        Route::post('nomina/{idNomina}/estado-rechazar',            'NominasController@api_rechazarNomina');
        Route::post('nomina/{idNomina}/estado-informar',            'NominasController@api_informarNomina');
        Route::post('nomina/{idNomina}/estado-rectificar',          'NominasController@api_rectificarNomina');

        // API AUDITORIAS
        Route::post('auditoria/nuevo', 'AuditoriasController@api_nuevo');
        Route::put('auditoria/{idAuditoria}', 'AuditoriasController@api_actualizar');
        Route::delete('auditoria/{idAuditoria}', 'AuditoriasController@api_eliminar');
        
        // API VISTA GENERAL
        Route::get('vista-general/nominas-inventarios',    'VistaGeneralController@api_obtenerNominasAuditorias');

        // API ACTIVO FIJO
        Route::get('api/activo-fijo/productos/buscar',              'ActivosFijosController@api_productos_buscar');
        Route::get('api/activo-fijo/articulos/buscar',              'ActivosFijosController@api_articulos_buscar');
        Route::get('api/activo-fijo/articulos/buscar-barra',        'ActivosFijosController@api_articulos_buscarBarra');
        Route::post('api/activo-fijo/articulos/entregar',           'ActivosFijosController@api_articulos_entregar_a_almacen');
        Route::post('api/activo-fijo/articulos/transferir',         'ActivosFijosController@api_articulos_transferir');
        Route::get('api/activo-fijo/almacenes/buscar',              'ActivosFijosController@api_almacenes_buscar');
        Route::post('api/activo-fijo/almacen/nuevo',                'ActivosFijosController@api_almacen_nuevo');
        Route::get('api/activo-fijo/preguias/buscar',               'ActivosFijosController@api_preguias_buscar');
        Route::get('api/activo-fijo/preguia/{idPreguia}',           'ActivosFijosController@api_preguia_fetch');
        Route::post('api/activo-fijo/preguia/{idPreguia}/devolver', 'ActivosFijosController@api_preguia_devolver');
        Route::get('api/activo-fijo/responsables/buscar',           'ActivosFijosController@api_responsables_buscar');

        // API USUARIOS
        Route::put('usuario/{idUsuario}',           'PersonalController@api_actualizar');
        Route::get('usuarios/buscar',               'PersonalController@api_buscar');
        Route::post('usuarios/nuevo-operador',      'PersonalController@api_nuevoOperador');
        // API USUARIOS-ROLES
        Route::post('usuario/{idUsuario}/role/{idRole}',            'PersonalController@api_nuevo_rol');
        Route::delete('usuario/{idUsuario}/role/{idRole}',          'PersonalController@api_delete_rol');
        // API ROLES-PERMISOS
        Route::post('permission/{idPermission}/role/{idRole}',      'PersonalController@api_nuevo_permiso');
        Route::delete('permission/{idPermission}/roles/{idRole}',   'PersonalController@api_delete_permiso');
        // API MANTENEDOR PERMISOS
        Route::post('permission/nuevo',                              'PersonalController@api_nuevoPermission');
        Route::put('permission/{idPermission}/editar',              'PersonalController@api_actualizarPermission');
        Route::delete('permission/{idPermission}',                   'PersonalController@api_eliminarPermission');
        //API MANTENEDOR ROLES
        Route::post('roles',                        'PersonalController@api_nuevoRole');
        Route::put('role/{idRole}',                 'PersonalController@api_actualizarRole');
        Route::delete('role/{idRole}',              'PersonalController@api_eliminarRole');

        // API GEO (DESARROLLO DETENIDO)
//        Route::get('geo/comunas',      'GeoController@api_getComunas');
        //  Route::get('stock/leerArchivo',             'StockController@api_leerArchivo');
        Route::post('stock/upload',                 'StockController@api_uploadArchivo');
        Route::post('stock/pegar',                  'StockController@api_pegarDatos');
    });
    /*
    |--------------------------------------------------------------------------
    | API's PUBLICAS, UTILIZADAS POR LA OTRA APLICACION
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix'=>'api'], function(){
        // API INVENTARIOS
        Route::get('inventarios/buscar',                   'InventariosController@api_buscar');
        Route::post('inventarios/informar-archivo-final',  'InventariosController@api_informarArchivoFinal');
        // API DE NOMINAS
        Route::post('nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible', 'NominasController@api_informarDisponible');
        Route::post('nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-nomina-pago','NominasController@api_informarNominaPago');
        // API AUDITORIAS
        Route::get('auditoria/mes/{annoMesDia}/cliente/{idCliente}',        'AuditoriasController@api_getPorMesYCliente');
        Route::get('auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}',    'AuditoriasController@api_getPorRangoYCliente');
        Route::get('auditoria/{fecha1}/al/{fecha2}/auditor/{idCliente}',  'AuditoriasController@api_getPorRangoYAuditor');
        Route::get('auditoria/cliente/{idCliente}/dia/{annoMesDia}/estado-general',   'AuditoriasController@api_estadoGeneral');
        Route::post('auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado', 'AuditoriasController@api_informarRealizado');
        Route::post('auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-fecha', 'AuditoriasController@api_informarFecha');
        // API USUARIOS
        Route::get('usuario/{idUsuario}/roles', 'PersonalController@api_getRolesUsuario');
        Route::get('usuarios/descargar-excel',  'PersonalController@excel_descargarTodos');
    });
});