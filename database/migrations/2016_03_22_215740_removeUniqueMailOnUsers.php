<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUniqueMailOnUsers extends Migration {
    public function up() {

        Schema::table('users', function(Blueprint $table){
            $table->dropUnique('users_email_unique');
            $table->unique('RUN');
        });
    }
    
    public function down() {
        Schema::table('users', function(Blueprint $table){
            $table->unique('email');
            $table->dropUnique('users_run_unique');
        });
    }
}
