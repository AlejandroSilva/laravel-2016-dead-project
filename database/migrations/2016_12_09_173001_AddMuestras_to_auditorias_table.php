<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMuestrasToAuditoriasTable extends Migration {
    public function up(){
        DB::transaction(function() {
            Schema::table('auditorias', function (Blueprint $table) {
                $table->text('nombreArchivoIrd');
                $table->text('nombreOriginalIrd');
                $table->text('nombreArchivoVencimiento');
                $table->text('nombreOriginalVencimiento');
            });
        });
    }
    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('nombreArchivoIrd');
            $table->dropColumn('nombreOriginalIrd');
            $table->dropColumn('nombreArchivoVencimiento');
            $table->dropColumn('nombreOriginalVencimiento');
        });
    }
}