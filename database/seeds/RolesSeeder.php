<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder {
    public function run(){
        DB::table('roles')->insert([
            ['id'=> 1, 'name'=>'Administrador',    'display_name'=>'Admin',        'description'=>'Administrador de la plataforma'],
            ['id'=> 2, 'name'=>'Lider',            'display_name'=>'Lider',        'description'=>'Lider de los inventarios'],
            ['id'=> 3, 'name'=>'Captador',         'display_name'=>'Captador',     'description'=>'Capta operadores para los inventarios'],
            ['id'=> 4, 'name'=>'Operador',         'display_name'=>'Operador',     'description'=>'Opera los capturadores en los inventarios'],
            ['id'=> 5, 'name'=>'Supervisor',       'display_name'=>'Supervisor',   'description'=>'Supervisa a los operadores en los inventarios'],
        ]);
    }
}
