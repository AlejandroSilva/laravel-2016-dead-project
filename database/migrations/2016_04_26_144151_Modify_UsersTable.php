<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
// Modelos
use App\User;

class ModifyUsersTable extends Migration {
    public function up() {
        Schema::table('users', function(Blueprint $table){
            // NOMBRE (1)               OK
            // NOMBRE (2)               OK
            // APELLIDO PATERNO         OK
            // APELLIDO MATERNOCOD.     OK
            // USUARIO                  (cambiar campo RUN a USUARIO)
            $table->renameColumn('RUN', 'usuarioRUN');
            // DIGITO VERIFICADOR       // agregar campo
            $table->char('usuarioDV', 1);
            // CORREO CONTACTO          // cambiar el campo EMAIL a emailSEI
            $table->string('emailSEI', 60)->default('');
            // CORREO PERSONAL          // agregar emailPersonal
            $table->string('emailPersonal', 60)->default('');
            // DIRECCION                // agregar direccion
            $table->string('direccion', 150)->default('');
            // CIUDAD / REGION          // agregar cutComuna (FK de la tabla Comunas)
            $table->integer('cutComuna')->unsigned();
            // FONO                     // renombrar telefono1 a telefono
            $table->renameColumn('telefono1', 'telefono')->default('');
            // FONO EMERGENCIA          // renonbrar telefono2 a telefonoEmergencia
            $table->renameColumn('telefono2', 'telefonoEmergencia')->default('');
            // DIA NAC. / MES NAC. / AÑO NAC.   OK
            // CAMPO CONTRATO           // eliminar campo contratado
            $table->dropColumn('contratado');
            // TIPO CONTRATO            // agregar campo
            $table->string('tipoContrato', 30);
            // INICIO CONTRATO          // agregar campo
            $table->date('fechaInicioContrato');
            // FECHA CERT.ANTECEDENTES  // agregar campo
            $table->date('fechaCertificadoAntecedentes');
            // BANCO                    // agregar campo
            $table->string('banco', 30);
            // TIPO CTA.                // agregar campo
            $table->string('tipoCuenta', 30);
            // Nª CTA                   // agregar campo
            $table->string('numeroCuenta', 20);
        });
        Schema::table('users', function(Blueprint $table){
            foreach(User::all() as $user) {
                // Luego de agregar el campo cutComuna, asignar una comuna por defecto y hacer la referencia a la Tabla Comunas
                $user->cutComuna = 7301; // Curico

                // Luego de agregar el campo emailSEI, mover los datos a este campo
                $user->emailSEI = $user->email;
                $user->save();
            }
            $table->foreign('cutComuna')->references('cutComuna')->on('comunas');
        });
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    public function down() {
        // agregar los campos
        Schema::table('users', function(Blueprint $table) {
            $table->string('email', 60)->default('');
        });
        // migrar los datos
        Schema::table('users', function() {
            foreach (User::all() as $user) {
                $user->email = $user->emailSEI;
                $user->save();
            }
        });
        Schema::table('users', function(Blueprint $table){
            // NOMBRE (1)               OK
            // NOMBRE (2)               OK
            // APELLIDO PATERNO         OK
            // APELLIDO MATERNOCOD.     OK
            // USUARIO                  (cambiar campo USUARIO a RUN)
            $table->renameColumn('usuarioRUN', 'RUN');
            // DIGITO VERIFICADOR       // eliminar campo
            $table->dropColumn('usuarioDV');
            // CORREO CONTACTO          // eliminar el campo
            $table->dropColumn('emailSEI');
            // CORREO PERSONAL          // eliminar emailPersonal
            $table->dropColumn('emailPersonal');
            // DIRECCION                // eliminar direccion
            $table->dropColumn('direccion');
            // CIUDAD / REGION          // eliminar cutComuna
            $table->dropForeign('users_cutcomuna_foreign');
            $table->dropColumn('cutComuna');
            // FONO                     // renombrar telefono1 a telefono
            $table->renameColumn('telefono', 'telefono1');
            // FONO EMERGENCIA          // renonbrar telefono2 a telefonoEmergencia
            $table->renameColumn('telefonoEmergencia', 'telefono2');
            // DIA NAC. / MES NAC. / AÑO NAC.   OK
            // CAMPO CONTRATO           // eliminar campo contratado
            $table->boolean('contratado')->default(false);
            // TIPO CONTRATO            // eliminar campo
            $table->dropColumn('tipoContrato');
            // INICIO CONTRATO          // eliminar campo
            $table->dropColumn('fechaInicioContrato');
            // FECHA CERT.ANTECEDENTES  // eliminar campo
            $table->dropColumn('fechaCertificadoAntecedentes');
            // BANCO                    // eliminar campo
            $table->dropColumn('banco');
            // TIPO CTA.                // eliminar campo
            $table->dropColumn('tipoCuenta');
            // Nª CTA                   // eliminar campo
            $table->dropColumn('numeroCuenta');
        });
    }
}

