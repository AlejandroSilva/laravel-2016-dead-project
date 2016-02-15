<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function(){
    return view('maptest');
});

Route::get('/hello', function(){
//   return App\Zonas::find(1)->regiones;
//   return App\Regiones::find(7)->provincias;
//   return App\Regiones::find(7)->zona;
//   return App\Provincias::find(73)->comunas;
   return App\Locales::find(636)->direccion;
//   return App\Clientes::find(9)->locales;
});
Route::get('import', function(){
    DB::transaction(function() {
        LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesFCV.csv');
        LocalesTableSeeder::parseAndInsert('/home/asilva/Escritorio/localesPreunic.csv');
    });
});

//al hacer un seed "coquimbo" esta dos veces, hacer un constrain de unique con nombre/cliente
//hacer un constrain con numero/cliente


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