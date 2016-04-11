<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHoraLLegadaAuditoria extends Migration {
    public function up() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->time('horaPresentacionAuditor')->nullable();
            $table->time('horaTermino')->nullable();
        });
    }

    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('horaPresentacionAuditor');
            $table->dropColumn('horaTermino');
        });
    }
}
