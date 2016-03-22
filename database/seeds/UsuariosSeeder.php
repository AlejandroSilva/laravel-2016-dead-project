<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UsuariosSeeder extends Seeder {
    public function run() {
        $rolAdministrador = Role::find(1);
        $administradores = [
            ['RUN'=>'16.589.300-K', 'nombre1'=>'Alejandro', 'nombre2'=>'Alfonso',   'apellidoPaterno'=>'Silva',     'apellidoMaterno'=>'Aliaga']
        ];
        $admin = User::create($administradores[0]);
        $admin->attachRole($rolAdministrador);

        $rolLider    = Role::find(2);
        $lideres = [
            ['16.023.826-7', 'ALDO',    'ANDRES',       'SALAS',     'TECAS'],
            ['18.204.398-2', 'DANILO',  'ESTEBAN',      'BAHAMONDE', 'HERNANDEZ'],
            ['16.857.772-9', 'DAVID',   'HANS',         'MUÑOZ',     'HERNANDEZ'],
            ['18.154.984-K', 'ELIECER', 'ALEJANDRO',    'BAEZA',     'SANDOVAL'],
            ['17.154.801-2', 'MANUEL',  '',             'VALENZUELA','PEÑA'],
            ['15.130.545-8', 'PAUL',    '',             'RIVERA',    'MATURANA'],
            ['16.164.196-0', 'YOAN',    '',             'CORTES',    'FICA'],
            ['15.921.678-0', 'JOSE',    'LUIS',         'AGUILAR',   ''],
            ['16.335.703-8', 'VICTOR ', '',             'MUÑOZ',     ''],
            ['17.610.808-8', 'KAREN',   '',             'RIQUELME',  ''],
            ['18.924.726-5', 'BYRON',   '',             'LONCOMA',   ''],
        ];
        UsuariosSeeder::insertUserAs($lideres, $rolLider);

//        $supervisores = [
//            ['16.023.826-7', 'nombre1'=>'ALDO',      'nombre2'=>'ANDRES',    'apellidoPaterno'=>'SALAS',     'apellidoMaterno'=>'TECAS'],
//        ];
//
        $rolCaptador = Role::find(3);
        $captadores = [
            // //['17.610.808-8',    'KAREN',        '',         'RIQUELME', ''],   // ya esta como lider
            ['15.101.249-3',    'CECILIA',      '',         'LANAS',    'JAMET'],
            ['11.957.809-4',    'NESTOR ',      '',         'VERA',     'MORA'],
            ['17.653.853-8',    'JENNIPHER',    '',         'RAMIREZ',  'TRIVIÑOS'],
            ['18.892.112-4',    'Solangel',     'Gabriela', 'Araya',    'García'],
            ['16.101.664-0',    'ELIZABETH ',   '',         'ALARCON',  ''],
            ['17.948.742-K',    'JESSICA',      '',         'ARROYO',   ''],
            // sin rut no me sirven
//            ['',                'DANIELA',      '',         'MUÑOZ',    ''],
//            ['',		        'CLAUDIA',      '',         'SALAZAR',  ''],
//            ['',		        'moreno',       '',         '',         ''],
//            ['',		        'SEI',          '',         '',         ''],
        ];
        UsuariosSeeder::insertUserAs($captadores, $rolCaptador);

        $rolOperador = Role::find(4);
        $operadores = [
            ['17.368.831-8',    'RUTH',         '',         'MUHLENBROCK',  ''],
            ['17.037.159-3',    'ANDREA',       '',         'MERY',         'ZULETA'],
            ['17.624.952-8',    'FRANCHESKA',   '',         'VARAS',        'DIAZ'],
            ['17.722.731-5',    'MARION',       '',         'SANTANDER',    'PINCHEIRA'],
            ['18.632.302-5',    'ARACELI',      '',         'VALENZUELA',   'BARRAZA'],
            ['16.687.448-3',    'SEBASTIAN',    '',         'ROJAS',        'GALVEZ'],
            ['16.969.284-K',    'NATHALIE',     '',         'COYAPAE',      ''],
            ['18.709.867-K',    'HAROL',        '',         'MONTALVAN',    'AGUIRRE'],
            ['18.200.929-6',    'JUAN',         '',         'TORRES',       'ARGANDOÑA']
        ];
        UsuariosSeeder::insertUserAs($operadores, $rolOperador);
    }

    private static function insertUserAs($users, $role){
        foreach($users as $user){
            $usuario = User::create([
                'RUN'=>$user[0], 'nombre1'=>$user[1], 'nombre2'=>$user[2], 'apellidoPaterno'=>$user[3], 'apellidoMaterno'=>$user[4]
            ]);
            $usuario->attachRole($role);
        }
    }
}
