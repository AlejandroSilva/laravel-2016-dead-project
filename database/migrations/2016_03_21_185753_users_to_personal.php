<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersToPersonal extends Migration {

    public function up(){
        Schema::drop('users');
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');             // agregado en otra migracion, dejar
            $table->string('RUN', 15);
            $table->string('email', 60)->unique();

            // datos personales
            $table->string('nombre1', 20);
            $table->string('nombre2', 20);
            $table->string('apellidoPaterno', 20);
            $table->string('apellidoMaterno', 20);
            $table->date('fechaNacimiento');
            // datos de la empresa
            $table->string('telefono1', 20);
            $table->string('telefono2', 20);
            $table->boolean('contratado')->default(false);
            $table->boolean('bloqueado')->default(false);
            
            $table->string('password', 60);       // agregado en otra migracion, dejar
            $table->rememberToken();              // agregado en otra migracion, dejar
            $table->timestamps();                 // agregado en otra migracion, dejar
        });
    }

    public function down(){
//        Schema::table('users', function (Blueprint $table) {
//            $table->increments('id');
//        });
    }
}
