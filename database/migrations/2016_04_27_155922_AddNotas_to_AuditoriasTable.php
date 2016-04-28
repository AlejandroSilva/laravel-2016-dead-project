<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Auditorias;

class AddNotasToAuditoriasTable extends Migration {
    
    public function up() {
        // creamos el campo "estadoAnalisis" y "notaAnalisis"
        Schema::table('auditorias', function (Blueprint $table) {
            $table->string('estadoAnalisis', 20)->default('Pendiente');
            $table->text('notaAnalisis')->default('');
        });
        // "migramos" los datos del campo "aprovada"
        foreach(Auditorias::all() as $auditoria){
            $auditoria->estadoAnalisis = $auditoria->aprovada=="1"? "Aprobado" : "Pendiente";
            $auditoria->save();
        }
        Schema::table('auditorias', function (Blueprint $table) {
            // fechaAuditoria       // dejar tal cual esta
            $table->dropColumn('aprovada');             // eliminar, no se ocupa en ningun lado
            $table->dropColumn('realizada');            // eliminar, no se ocupa en ningun lado
            $table->dropColumn('realizadaInformada');   // eliminar
        });
    }

    public function down() {
        // crear las tablas "realizada", "aprovada", "realizadaInformada"
        Schema::table('auditorias', function (Blueprint $table) {
            $table->boolean('realizada')->default(false);
            $table->boolean('aprovada')->default(false);
            $table->boolean('realizadaInformada')->default(false);
        });
        foreach(Auditorias::all() as $auditoria){
            $auditoria->aprovada = $auditoria->estadoAnalisis==='Aprobado'? true: false;
        }
        // eliminar las columnas "estadoAnalisis" y "notaAnalisis"
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropColumn('estadoAnalisis');
            $table->dropColumn('notaAnalisis');
        });
    }
}
