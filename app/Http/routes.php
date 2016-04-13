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
        //Route::get('locales', function(){return view('operacional.clientes.locales');});
    });

    // API REST
    Route::get('api/clientes', 'ClientesController@api_getClientes');
    Route::get('api/clientes/locales', 'ClientesController@api_getClientesWithLocales');
    Route::get('api/locales/{idLocal}', 'LocalesController@api_getLocal');
    Route::get('api/locales/{idLocal}/verbose', 'LocalesController@api_getLocalVerbose');

    /*
    |--------------------------------------------------------------------------
    | Programación de Inventarios Generales
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'programacionIG', 'middleware' => ['auth']], function(){
        Route::get('/',                  'ProgramacionController@showIndex');
        Route::get('/mensual',          'ProgramacionController@showMensual');
        Route::get('/mensual/pdf/{mes}','ProgramacionController@descargarProgramaMensual');
        Route::get('/semanal',          'ProgramacionController@showSemanal');
    });

    
    /*
    |--------------------------------------------------------------------------
    | Programación de Auditoria de Inventarios
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'programacionAI', 'middleware' => ['auth']], function() {
        Route::get('/',                 'ProgramacionAIController@showIndex');
        Route::get('/mensual',          'ProgramacionAIController@showMensual');
        Route::get('/mensual/pdf/{mes}', 'ProgramacionAIController@descargarProgramaMensual');
        Route::get('/semanal',          'ProgramacionAIController@showSemanal');
    });
    
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
    Route::get('api/inventario/mes/{annoMesDia}',       'InventariosController@api_getPorMes');
    Route::get('api/inventario/{fecha1}/al/{fecha2}',   'InventariosController@api_getPorRango');
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
    Route::get('api/auditoria/mes/{annoMesDia}',            'AuditoriasController@api_getPorMes');
    Route::put('api/auditoria/{idAuditoria}',               'AuditoriasController@api_actualizar');
    Route::delete('api/auditoria/{idAuditoria}',            'AuditoriasController@api_eliminar');
    Route::get('api/auditoria/{fecha1}/al/{fecha2}/cliente/{idCliente}',   'AuditoriasController@api_getPorRangoYCliente');

    /*
    |--------------------------------------------------------------------------
    | Gestion de Personal
    |--------------------------------------------------------------------------
    |*/
    Route::group(['prefix' => 'personal', 'middleware' => ['auth']], function() {
        Route::get('lista',       'PersonalController@show_listaPersonal')->name('personal.lista');
        Route::get('nuevo',       'PersonalController@show_formulario')->name('personal.nuevo');
        Route::post('nuevo',      'PersonalController@show_postFormulario');
        Route::get('test',        'PersonalController@test');
    });
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