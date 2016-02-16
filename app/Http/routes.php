<?php

/*
|--------------------------------------------------------------------------
| Administracion de Clientes
|--------------------------------------------------------------------------
|*/
Route::get('admin/clientes', 'ClientesController@verLista')->name('admin.clientes.lista');
Route::get('admin/locales', function(){
    return view('administracion.locales.verLista');
});

/*
|--------------------------------------------------------------------------
| Gestión de Inventarios
|--------------------------------------------------------------------------
|*/
Route::get('inventario/programa', function(){
    return view('inventario.programa');
});
Route::get('inventario/inventario', function(){
    return view('inventario.inventario');
});
Route::get('inventario/nominas', function(){
    return view('inventario.nominas');
});
Route::get('inventario/nominasFinales', function(){
    return view('inventario.nominasFinales');
});

/*
|--------------------------------------------------------------------------
| Gestion de Personal
|--------------------------------------------------------------------------
|*/
Route::get('usuarios/usuarios', function(){
    return view('usuarios.usuarios');
});
Route::get('usuarios/operadores', function(){
    return view('usuarios.operadores');
});
/*
|--------------------------------------------------------------------------
| Otros
|--------------------------------------------------------------------------
|*/
Route::get('/', function () {
    return view('child');
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