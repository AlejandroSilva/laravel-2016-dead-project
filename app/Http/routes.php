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
    Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function(){
        Route::get('clientes', 'ClientesController@show_Lista')->name('admin.clientes.lista');
        Route::get('locales',  'LocalesController@show_mantenedor')->name('admin.locales.lista');
    });

    // API REST
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
    Route::get('/pdf/inventarios/{mes}/cliente/{idCliente}',    'InventariosController@descargarPDF_porMes');
    Route::get('/pdf/inventarios/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',   'InventariosController@descargarPDF_porRango');

    
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
    Route::get('/pdf/auditorias/{mes}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porMes');
    Route::get('/pdf/auditorias/{fechaInicial}/al/{fechaFinal}/cliente/{idCliente}',     'AuditoriasController@descargarPDF_porRango');
    
    /*
    |--------------------------------------------------------------------------
    | Gestión de Inventarios
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'inventario', 'middleware' => ['auth']], function() {
        Route::get('/',                 'InventariosController@showIndex');
        Route::get('/nuevo',            'InventariosController@showNuevo');
        Route::get('/lista',            'InventariosController@showLista');
    });
    Route::post('api/inventario/nuevo',                 'InventariosController@api_nuevo');
    Route::get('api/inventario/{annoMesDia}/cliente/{idCliente}',  'InventariosController@api_getPorMesYCliente');
    Route::get('api/inventario/{fecha1}/al/{fecha2}',   'InventariosController@api_getPorRango');
    // No modificar esta ruta, Esteban la utiliza
    Route::get('api/inventario/{fecha1}/al/{fecha2}/cliente/{idCliente}',   'InventariosController@api_getPorRangoYCliente');
    Route::get('api/inventario/{idInventario}',         'InventariosController@api_get');
    Route::put('api/inventario/{idInventario}',         'InventariosController@api_actualizar');
    Route::delete('api/inventario/{idInventario}',      'InventariosController@api_eliminar');
    Route::get('api/nomina/{idNomina}',                 'NominasController@api_get');
    Route::put('api/nomina/{idNomina}',                 'NominasController@api_actualizar');


    Route::group(['prefix' => 'nominas', 'middleware' => ['auth']], function() {
        Route::get('/',               'NominasController@showNominas');
    });
    Route::group(['prefix' => 'nomFinales', 'middleware' => ['auth']], function() {
        Route::get('/',            'NominasController@showNominasFinales');
    });

    /*
    |--------------------------------------------------------------------------
    | Gestión de Auditorias
    |--------------------------------------------------------------------------
    |*/
    Route::post('api/auditoria/nuevo',                      'AuditoriasController@api_nuevo');
    Route::put('api/auditoria/{idAuditoria}',               'AuditoriasController@api_actualizar');
    Route::delete('api/auditoria/{idAuditoria}',            'AuditoriasController@api_eliminar');
    Route::get('api/auditoria/mes/{annoMesDia}/cliente/{idCliente}',     'AuditoriasController@api_getPorMesYCliente');
    Route::get('api/auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}', 'AuditoriasController@api_getPorRangoYCliente');

    /*
    |--------------------------------------------------------------------------
    | Gestion de Personal
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'personal', 'middleware' => ['auth']], function() {
        Route::get('lista',             'PersonalController@show_listaPersonal')->name('personal.lista');
        Route::get('nuevo',             'PersonalController@show_formulario')->name('personal.nuevo');
        Route::post('nuevo',            'PersonalController@show_postFormulario');
    });
    // No modificar esta ruta, Esteban la utiliza
    Route::get('api/personal/{idUsuario}/roles', 'PersonalController@api_getRolesUsuario');
    /*
    |--------------------------------------------------------------------------
    | Otros
    |--------------------------------------------------------------------------
    |*/
    //Route::get('/map', function(){return view('maptest');});
    Route::get('import', function(){
        DB::transaction(function() {
//            LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesFCV.csv');
//            LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesPreunic.csv');
            LocalesTableSeeder::parseAndInsert(public_path('seedFiles/localesSalcobrand.csv'));
//            LocalesTableSeeder::actualizarStock( public_path('actualizarStock/Stock al 30-03-2016.xlsx - Stock al 30-03.csv'));
        });
    });
});