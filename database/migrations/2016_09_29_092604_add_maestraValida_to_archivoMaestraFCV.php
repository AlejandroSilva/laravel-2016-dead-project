<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaestraValidaToArchivoMaestraFCV extends Migration
{
    public function up(){
        Schema::table('archivo_maestra_fcv', function(Blueprint $table){
            $table->boolean('maestraValida')->default(false)->after('nombreOriginal');
        });
    }

    public function down(){
        Schema::table('archivo_maestra_fcv', function(Blueprint $table){
           $table->dropColumn('maestraValida'); 
        });
    }
}
