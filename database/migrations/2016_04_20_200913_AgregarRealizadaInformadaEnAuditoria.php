<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgregarRealizadaInformadaEnAuditoria extends Migration {
    public function up() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->boolean('realizadaInformada')->default(false);
        });
    }
    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('realizadaInformada');
        });
    }
}
