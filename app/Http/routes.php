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
        Route::get('admin/clientes',      'ClientesController@show_Lista')->name('admin.clientes.lista');
        Route::get('admin/locales',       'LocalesController@show_mantenedor')->name('admin.locales.lista');
        // INVENTARIOS - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('inventario',                  'InventariosController@showIndex');
//        Route::get('inventario/nuevo',            'InventariosController@showNuevo');
//        Route::get('inventario/lista',            'InventariosController@showLista');
        // INVENTARIOS - PROGRAMACION IG
        Route::get('programacionIG',                  'InventariosController@showProgramacionIndex');
        Route::get('programacionIG/mensual',          'InventariosController@showProgramacionMensual');
        Route::get('programacionIG/semanal',          'InventariosController@showProgramacionSemanal');
        // INVENTARIOS - DESCARGA DE PDF
        Route::get('/pdf/inventarios/{mes}/cliente/{idCliente}',         'InventariosController@descargarPDF_porMes');
        Route::get('/pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',   'InventariosController@descargarPDF_porRango');
        // INVENTARIO - MANTENEDOR DE NOMINAS
        Route::get('programacionIG/nomina/{idNomina}',        'NominasController@show_nomina');
        // AUDITORIAS - PROGRAMACION AI
        Route::get('programacionAI',                 'AuditoriasController@showProgramacionIndex');
        Route::get('programacionAI/mensual',          'AuditoriasController@showMensual');
        Route::get('programacionAI/semanal',          'AuditoriasController@showSemanal');
        // AUDITORIAS - DESCARGA DE PDF
        Route::get('pdf/auditorias/{mes}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porMes');
        Route::get('pdf/auditorias/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porRango');
        // USUARIOS - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('personal/nuevo',             'PersonalController@show_formulario')->name('personal.nuevo');
        // GEO - MANTENEDOR (DESARROLLO DETENIDO)
//        Route::get('geo',             'GeoController@show_index');//->name('geo.index');
    });

    /*
    |--------------------------------------------------------------------------
    | API's PROTEGIDAS SOLO A USUARIOS
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix'=>'api',  'middleware'=>['authAPI']], function() {
        // API CLIENTES Y LOCALES
        Route::get('clientes',                      'ClientesController@api_getClientes');
        Route::get('cliente/{idCliente}/locales',   'ClientesController@api_getLocales');
        Route::get('clientes/locales',              'ClientesController@api_getClientesWithLocales');
        Route::get('locales/{idLocal}',             'LocalesController@api_getLocal');
        Route::get('locales/{idLocal}/verbose',     'LocalesController@api_getLocalVerbose');
        // API INVENTARIOS
        Route::post('inventario/nuevo',                 'InventariosController@api_nuevo');
        Route::get('inventario/{idInventario}',         'InventariosController@api_get');
        Route::put('inventario/{idInventario}',         'InventariosController@api_actualizar');
        Route::delete('inventario/{idInventario}',      'InventariosController@api_eliminar');
        // API DE NOMINAS
        Route::get('nomina/{idNomina}',                 'NominasController@api_get');
        Route::put('nomina/{idNomina}',                 'NominasController@api_actualizar');
        // API AUDITORIAS
        Route::post('auditoria/nuevo', 'AuditoriasController@api_nuevo');
        Route::put('auditoria/{idAuditoria}', 'AuditoriasController@api_actualizar');
        Route::delete('auditoria/{idAuditoria}', 'AuditoriasController@api_eliminar');
        // API USUARIOS
        Route::get('usuario/buscar',            'PersonalController@api_buscar');
        Route::post('usuario/nuevo-operador',   'PersonalController@api_nuevoOperador');
        Route::put('usuario/{idUsuario}',       'PersonalController@api_actualizar');
        // API GEO (DESARROLLO DETENIDO)
//        Route::get('geo/comunas',      'GeoController@api_getComunas');
    });

    /*
    |--------------------------------------------------------------------------
    | API's PUBLICAS, UTILIZADAS POR LA OTRA APLICACION
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix'=>'api'], function(){
        // API INVENTARIOS
        Route::get('inventarios/buscar',                 'InventariosController@api_buscar');
        Route::post('inventarios/informar-archivo-final',  'InventariosController@api_informarArchivoFinal');
        // API DE NOMINAS
        Route::post('nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible', 'NominasController@api_informarDisponible');
        Route::post('nomina/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-manual/{fecha2}', 'NominasController@api_informarDisponible2');
        // API AUDITORIAS
        Route::get('auditoria/mes/{annoMesDia}/cliente/{idCliente}',        'AuditoriasController@api_getPorMesYCliente');
        Route::get('auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}',    'AuditoriasController@api_getPorRangoYCliente');
        Route::get('auditoria/{fecha1}/al/{fecha2}/auditor/{idCliente}',  'AuditoriasController@api_getPorRangoYAuditor');
        Route::get('auditoria/cliente/{idCliente}/dia/{annoMesDia}/estado-general',   'AuditoriasController@api_estadoGeneral');
        Route::post('auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado', 'AuditoriasController@api_informarRealizado');
        Route::post('auditoria/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-fecha', 'AuditoriasController@api_informarFecha');
        // API USUARIOS - RUTAS PUBLICAS UTILIZADAS POR LA OTRA APLICACION
        Route::get('usuario/{idUsuario}/roles', 'PersonalController@api_getRolesUsuario');
    });
});