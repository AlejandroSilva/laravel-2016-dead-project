<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyFechaAuditoriaOnAuditoriaTable extends Migration {
    public function up() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table
                ->date('fechaAuditoria')
                ->nullable(false)
                ->change();
                //->default('0000-00-00');
        });
    }
    public function down() {
        Schema::table('auditorias', function (Blueprint $table) {
            $table
                ->date('fechaAuditoria')
                ->nullable(false)
                ->change();
        });
    }
}
