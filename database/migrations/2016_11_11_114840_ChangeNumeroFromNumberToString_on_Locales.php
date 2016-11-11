<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNumeroFromNumberToStringOnLocales extends Migration {

    public function up() {
        Schema::table('locales', function(Blueprint $table){
            $table->string('numero', 20)->change();
        });
    }

    public function down() {
        Schema::table('locales', function(Blueprint $table){
            $table->integer('numero')->change();
        });
    }
}
