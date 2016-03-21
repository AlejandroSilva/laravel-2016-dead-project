<?php

/*
|--------------------------------------------------------------------------
| Administracion de Clientes y sus locales
|--------------------------------------------------------------------------
|*/
// VISTAS
Route::get('admin/clientes', 'ClientesController@show_Lista')->name('admin.clientes.lista');
Route::get('admin/locales', function(){
    return view('operacional.clientes.locales');
});
// API REST
Route::get('api/clientes', 'ClientesController@api_getClientes');
Route::get('api/clientes/locales', 'ClientesController@api_getClientesWithLocales');
Route::get('api/locales/{idLocal}', 'LocalesController@api_getLocal');
Route::get('api/locales/{idLocal}/verbose', 'LocalesController@api_getLocalVerbose');

/*
|--------------------------------------------------------------------------
| Gestión de Inventarios
|--------------------------------------------------------------------------
|*/
Route::get('programacion',                  'ProgramacionController@showIndex');
Route::get('programacion/mensual',          'ProgramacionController@showMensual');
Route::get('programacion/semanal',          'ProgramacionController@showSemanal');
Route::get('inventario',                    'InventariosController@showIndex');
Route::get('inventario/nuevo',              'InventariosController@showNuevo');
Route::get('inventario/lista',              'InventariosController@showLista');
Route::post('api/inventario/nuevo',                 'InventariosController@api_nuevo');
Route::get('api/inventario/mes/{annoMesDia}',       'InventariosController@api_getPorMes');
Route::get('api/inventario/{fecha1}/al/{fecha2}',   'InventariosController@api_getPorRango');
Route::get('api/inventario/{idInventario}',         'InventariosController@api_get');
Route::put('api/inventario/{idInventario}',         'InventariosController@api_actualizar');

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
//   return App\Clientes::find(9)->locales;f
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