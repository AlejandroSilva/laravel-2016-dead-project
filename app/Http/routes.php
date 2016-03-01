<?php

/*
|--------------------------------------------------------------------------
| Administracion de Clientes y sus locales
|--------------------------------------------------------------------------
|*/
// VISTAS
Route::get('admin/clientes', 'ClientesController@verLista')->name('admin.clientes.lista');
Route::get('admin/locales', function(){
    return view('operacional.clientes.locales');
});
// API REST
Route::get('clientes', 'ClientesController@getClientes_json');
Route::get('clientes/locales', 'ClientesController@getClientesWithLocales_json');
Route::get('locales/{idLocal}', 'LocalesController@getLocal_json');
Route::get('locales/{idLocal}/verbose', 'LocalesController@getLocalVerbose_json');

/*
|--------------------------------------------------------------------------
| Gestión de Inventarios
|--------------------------------------------------------------------------
|*/
Route::get('programacion',          'ProgramacionController@showIndex');
Route::get('programacion/mensual',  'ProgramacionController@showMensual');
Route::get('programacion/semanal',  'ProgramacionController@showSemanal');
Route::get('inventario',            'InventariosController@index');
Route::get('inventario/lista',      'InventariosController@lista');
Route::get('inventario/nuevo',      'InventariosController@nuevo');
Route::post('inventario/nuevo',     'InventariosController@postNuevo');

Route::get('nominas',               function(){return view('operacional.nominas.nominas-index');});
Route::get('nomFinales',            function(){return view('operacional.nominasFinales.nominasFinales-index');});

/*
|--------------------------------------------------------------------------
| Gestion de Personal
|--------------------------------------------------------------------------
|*/
Route::get('personal/personal', function(){
    return view('operacional.personal.usuarios');
});
Route::get('personal/operadores', function(){
    return view('operacional.personal.operadores');
});
/*
|--------------------------------------------------------------------------
| Otros
|--------------------------------------------------------------------------
|*/
Route::get('/', function () {
    return redirect('/inventario');
});

Route::get('/map', function(){
    return view('maptest');
});

Route::get('/hello', function(){
//   return App\Zonas::find(1)->regiones;
//   return App\Regiones::find(7)->provincias;
//   return App\Regiones::find(7)->zona;
//   return App\Provincias::find(73)->comunas;
   return App\Locales::find(606)->direccion;
//   return App\Clientes::find(9)->locales;
});
Route::get('import', function(){
    DB::transaction(function() {
        LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesFCV.csv');
        LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesPreunic.csv');
    });
});

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

Route::group(['middleware' => ['web']], function () {
    //
});