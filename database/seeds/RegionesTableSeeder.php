<?php

use Illuminate\Database\Seeder;

class RegionesTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('regiones')->insert([
            ['cutRegion'=>15, 'idZona'=>1, 'nombre'=>'Región de Arica y Parinacota'],
            ['cutRegion'=> 1, 'idZona'=>1, 'nombre'=>'Región de Tarapacá'],
            ['cutRegion'=> 2, 'idZona'=>1, 'nombre'=>'Región de Antofagasta'],
            ['cutRegion'=> 3, 'idZona'=>2, 'nombre'=>'Región de Atacama'],
            ['cutRegion'=> 4, 'idZona'=>2, 'nombre'=>'Región de Coquimbo'],
            ['cutRegion'=> 5, 'idZona'=>3, 'nombre'=>'Región de Valparaíso'],
            ['cutRegion'=> 6, 'idZona'=>5, 'nombre'=>'Región del Libertador Gral. Bernardo O’Higgins'],
            ['cutRegion'=> 7, 'idZona'=>5, 'nombre'=>'Región del Maule'],
            ['cutRegion'=> 8, 'idZona'=>6, 'nombre'=>'Región del Biobío'],
            ['cutRegion'=> 9, 'idZona'=>6, 'nombre'=>'Región de La Araucanía'],
            ['cutRegion'=>14, 'idZona'=>7, 'nombre'=>'Región de Los Ríos'],
            ['cutRegion'=>10, 'idZona'=>7, 'nombre'=>'Región de Los Lagos'],
            ['cutRegion'=>11, 'idZona'=>7, 'nombre'=>'Región de Aysén del Gral. Carlos Ibáñez del Campo'],
            ['cutRegion'=>12, 'idZona'=>7, 'nombre'=>'Región de Magallanes y de la Antártica Chilena'],
            ['cutRegion'=>13, 'idZona'=>4, 'nombre'=>'Región Metropolitana de Santiago'],
        ]);
    }
}
