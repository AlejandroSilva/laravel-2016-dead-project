<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Modelos
use App\User;

class HotFixUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // crear el campo
        Schema::table('users', function(Blueprint $table){
            $table->string('email');
        });
        Schema::table('users', function(){
            // mover todos los datos
            foreach(User::all() as $user) {
                // Luego de agregar el campo emailSEI, mover los datos a este campo
                $user->email = $user->emailSEI;
                $user->save();
            }
        });
        // eliminar el campo antiguo
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('emailSEI');
        });
    }
    
    public function down() {
        // crear el campo 
        Schema::table('users', function(Blueprint $table){
            $table->string('emailSEI', 60)->default('');
        });
        // mover todos los datos
        foreach(User::all() as $user) {
            $user->emailSEI = $user->email;
            $user->save();
        }
        // eliminar el campo antiguo
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('email');
        });
    }
}
