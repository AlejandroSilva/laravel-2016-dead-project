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
    | Administracion de Clientes y sus locales
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'clientes', 'middleware' => ['auth']], function(){
        Route::get('/',                                   'ClientesController@show_Lista');
        Route::post('/',                                  'ClientesController@postFormulario');
        //Route::get('/cliente/{idCliente}/editar',                'ClientesController@api_get');
        Route::put('cliente/{idCliente}/editar',                 'ClientesController@api_actualizar');
        Route::get('locales',  'LocalesController@show_mantenedor')->name('admin.locales.lista');
        
    });

    /*
    |--------------------------------------------------------------------------
    | Administracion de formato_locales
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'formatoLocales', 'middleware' => ['auth']], function(){
        Route::get('/',                              'LocalesController@api_getFormatos');
        Route::post('/',                                    'LocalesController@postFormulario');
        Route::put('/formato/{idFormato}/editar',                'LocalesController@api_actualizar');

    });

    /*
    |--------------------------------------------------------------------------
    | Administracion de regiones
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'regiones', 'middleware' => ['auth']], function(){
        Route::get('/',                              'RegionesController@showMantenedorRegiones');
        Route::put('/{cutRegion}/editar',           'RegionesController@api_actualizar');
    });

    // API CLIENTES Y LOCALES
    Route::get('api/clientes',                      'ClientesController@api_getClientes');
    Route::get('api/cliente/{idCliente}/locales',   'ClientesController@api_getLocales');
    Route::get('api/clientes/locales',              'ClientesController@api_getClientesWithLocales');
    Route::get('api/locales/{idLocal}',             'LocalesController@api_getLocal');
    Route::get('api/locales/{idLocal}/verbose',     'LocalesController@api_getLocalVerbose');



    /*
    |--------------------------------------------------------------------------
    | Programación de Inventarios Generales
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'programacionIG', 'middleware' => ['auth']], function(){
        Route::get('/',                 'InventariosController@showProgramacionIndex');
        Route::get('/mensual',          'InventariosController@showProgramacionMensual');
        Route::get('/semanal',          'InventariosController@showProgramacionSemanal');
    });

    // DESCARGA DE PDF DE INVENTARIOS
    Route::group(['prefix' => '/pdf/inventarios/'], function(){
        Route::get('{mes}/cliente/{idCliente}',    'InventariosController@descargarPDF_porMes');
        Route::get('{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',   'InventariosController@descargarPDF_porRango');
    });

    // MANTENEDOR DE INVENTARIOS
    Route::group(['prefix' => 'inventario', 'middleware' => ['auth']], function() {
        Route::get('/',                 'InventariosController@showIndex');
        Route::get('/nuevo',            'InventariosController@showNuevo');
        Route::get('/lista',            'InventariosController@showLista');
    });

    // API INVENTARIOS
    Route::group(['prefix' => 'api/inventario'], function(){
        Route::post('nuevo',                 'InventariosController@api_nuevo');
        Route::get('{idInventario}',         'InventariosController@api_get');
        Route::put('{idInventario}',         'InventariosController@api_actualizar');
        Route::delete('{idInventario}',      'InventariosController@api_eliminar');
        // buscar inventarios
        Route::get('{annoMesDia}/cliente/{idCliente}',  'InventariosController@api_getPorMesYCliente');
        Route::get('{fecha1}/al/{fecha2}',              'InventariosController@api_getPorRango');   // ¿¿getPorRango ya no se utiliza??
        // RUTAS UTILIZADAS POR LA OTRA APLICACION
        Route::get('{fecha1}/al/{fecha2}/cliente/{idCliente}',   'InventariosController@api_getPorRangoYCliente');
        Route::get('{fecha1}/al/{fecha2}/lider/{idCliente}',     'InventariosController@api_getPorRangoYLider');
    });

    // API NOMINAS
    Route::group(['prefix' => 'api/nomina'], function(){
        Route::get('{idNomina}',                 'NominasController@api_get');
        Route::put('{idNomina}',                 'NominasController@api_actualizar');
        // RUTAS UTILIZADAS POR LA OTRA APLICACION
        Route::post('/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-disponible', 'NominasController@api_informarDisponible');
        Route::post('/cliente/{idCliente}/ceco/{CECO}/dia/{fecha}/informar-manual/{fecha2}', 'NominasController@api_informarDisponible2');
    });

    /*
    |--------------------------------------------------------------------------
    | Programación de Auditoria de Inventarios
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'programacionAI', 'middleware' => ['auth']], function() {
        Route::get('/',                 'AuditoriasController@showProgramacionIndex');
        Route::get('/mensual',          'AuditoriasController@showMensual');
        Route::get('/semanal',          'AuditoriasController@showSemanal');
    });

    // DESCARGA DE PDF DE AUDITORIAS
    Route::group(['prefix' => 'pdf/auditorias/'], function(){
        Route::get('{mes}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porMes');
        Route::get('{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porRango');
    });

    // API AUDITORIAS
    Route::group(['prefix' => 'api/auditoria'], function(){
        Route::post('nuevo',            'AuditoriasController@api_nuevo');
        Route::put('{idAuditoria}',     'AuditoriasController@api_actualizar');
        Route::delete('{idAuditoria}',  'AuditoriasController@api_eliminar');
        Route::get('mes/{annoMesDia}/cliente/{idCliente}',      'AuditoriasController@api_getPorMesYCliente');
        Route::get('{fecha1}/al/{fecha2}/cliente/{idCliente}',  'AuditoriasController@api_getPorRangoYCliente');
        // RUTAS UTILIZADAS POR OTRA APLICACION
        Route::get('/cliente/{idCliente}/dia/{annoMesDia}/estado-general',   'AuditoriasController@api_estadoGeneral');
        Route::get('{fecha1}/al/{fecha2}/auditor/{idCliente}',  'AuditoriasController@api_getPorRangoYAuditor');
        Route::post('/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-realizado', 'AuditoriasController@api_informarRealizado');
        Route::post('/cliente/{idCliente}/ceco/{CECO}/fecha/{fecha}/informar-fecha', 'AuditoriasController@api_informarFecha');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestion de Personal
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'personal', 'middleware' => ['auth']], function() {
        Route::get('nuevo',             'PersonalController@show_formulario')->name('personal.nuevo');
    });
    // API USUARIOS
    Route::group(['prefix' => 'api/usuario'], function(){
        Route::get('listado',           'PersonalController@api_getUsuarios')->name('per');
        Route::post('nuevo',            'PersonalController@api_crear');
        Route::put('{idUsuario}',       'PersonalController@api_actualizar');
        // RUTAS UTILIZADAS POR LA OTRA APLICACION
        Route::get('{idUsuario}/roles', 'PersonalController@api_getRolesUsuario');
    });

    /*
    |--------------------------------------------------------------------------
    | Otros
    |--------------------------------------------------------------------------
    |*/

    //    Route::group(['prefix' => 'nominas', 'middleware' => ['auth']], function() {
    //        Route::get('/',               'NominasController@showNominas');
    //    });
    //    Route::group(['prefix' => 'nomFinales', 'middleware' => ['auth']], function() {
    //        Route::get('/',            'NominasController@showNominasFinales');
    //    });

    //Route::get('/map', function(){return view('maptest');});
    /*
    Route::get('import', function(){
        DB::transaction(function() {
//            LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesFCV.csv');
//            LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesPreunic.csv');
            LocalesTableSeeder::parseAndInsert(public_path('seedFiles/localesSalcobrand.csv'));
//            LocalesTableSeeder::actualizarStock( public_path('actualizarStock/Stock al 30-03-2016.xlsx - Stock al 30-03.csv'));
        });
    });
    */
});