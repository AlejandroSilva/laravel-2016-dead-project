<?php

use Illuminate\Database\Seeder;

class ClientesTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('clientes')->insert([
            ['idCliente'=> 1, 'nombre'=>'Preunic',                  'nombreCorto'=>'PUC'],      // Grupo Salcobrand
            ['idCliente'=> 2, 'nombre'=>'Farmacia Cruz Verde',      'nombreCorto'=>'FCV'],
            // Colloky, Opaline, Dimension Azul, Creado por Nosotras como uno solo
            ['idCliente'=> 3, 'nombre'=>'Colloky',                  'nombreCorto'=>'CKY'],      // Grupo COLGRAM
            ['idCliente'=> 4, 'nombre'=>'Casaideas',                'nombreCorto'=>'CID'],
            ['idCliente'=> 5, 'nombre'=>'Salcobrand',               'nombreCorto'=>'FSB'],      // Grupo Salcobrand
            ['idCliente'=> 6, 'nombre'=>'Medcell',                  'nombreCorto'=>'MED'],      // Grupo Salcobrand

//            ['idCliente'=> 2, 'nombre'=>'Makeup',                   'nombreCorto'=>'MAKEUP'],       // Grupo Salcobrand
//            ['idCliente'=> 7, 'nombre'=>'Bata',                     'nombreCorto'=>'BATA'],
//            ['idCliente'=> 8, 'nombre'=>'Construmart',              'nombreCorto'=>'CONSTRUMART'],

        ]);
    }
}
