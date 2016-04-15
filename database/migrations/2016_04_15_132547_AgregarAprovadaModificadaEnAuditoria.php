<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarAprovadaModificadaEnAuditoria extends Migration {

    public function up() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->boolean('realizada')->default(false);
            $table->boolean('aprovada')->default(false);
        });
    }
    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('realizada');
            $table->dropColumn('aprovada');
        });
    }
}
