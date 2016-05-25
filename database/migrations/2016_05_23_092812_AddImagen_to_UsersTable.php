<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImagenToUsersTable extends Migration {
    public function up() {
        Schema::table('users', function(Blueprint $table){
            $table->text('imagenPerfil');
        });
    }

    public function down() {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('imagenPerfil');
        });
    }
}
