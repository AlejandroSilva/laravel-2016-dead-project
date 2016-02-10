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
            // Grupo Salcobrand
            ['idCliente'=> 1, 'nombre'=>'Preunic',                  'nombreCorto'=>'PREUNIC'],
            ['idCliente'=> 2, 'nombre'=>'Makeup',                   'nombreCorto'=>'MAKEUP'],
            ['idCliente'=> 3, 'nombre'=>'Salcobrand',               'nombreCorto'=>'SB'],
            ['idCliente'=> 4, 'nombre'=>'Medcell',                  'nombreCorto'=>'MEDCELL'],

            // Grupo COLGRAM
            // Colloky, Opaline, Dimension Azul, Creado por Nosotras como uno solo
            ['idCliente'=> 5, 'nombre'=>'Colloky',                  'nombreCorto'=>'COLLOKY'],

            // Otros
            ['idCliente'=> 6, 'nombre'=>'Casaideas',                'nombreCorto'=>'CASAIDEAS'],
            ['idCliente'=> 7, 'nombre'=>'Bata',                     'nombreCorto'=>'BATA'],
            ['idCliente'=> 8, 'nombre'=>'Construmart',              'nombreCorto'=>'CONSTRUMART'],
            ['idCliente'=> 9, 'nombre'=>'Farmacia Cruz Verde',      'nombreCorto'=>'FCV'],
        ]);
    }
}
