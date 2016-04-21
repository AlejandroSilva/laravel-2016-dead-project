<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFechaAuditoriaToAuditoriaTable extends Migration {
    public function up() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table
                ->date('fechaAuditoria')
                ->nullable()
                ->default(null);
        });
    }
    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('fechaAuditoria');
        });
    }
}
