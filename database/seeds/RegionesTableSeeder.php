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
            ['cutRegion'=>15, 'idZona'=>1, 'numero'=>'XV',
                'nombreCorto'=>'XV Arica y Parinacota', 'nombre'=>'Región de Arica y Parinacota'],
            ['cutRegion'=> 1, 'idZona'=>1, 'numero'=>'I',
                'nombreCorto'=>'I Tarapacá', 'nombre'=>'Región de Tarapacá'],
            ['cutRegion'=> 2, 'idZona'=>1, 'numero'=>'II',
                'nombreCorto'=>'II Antofagasta', 'nombre'=>'Región de Antofagasta'],
            ['cutRegion'=> 3, 'idZona'=>2, 'numero'=>'III',
                'nombreCorto'=>'III Atacama', 'nombre'=>'Región de Atacama'],
            ['cutRegion'=> 4, 'idZona'=>2, 'numero'=>'IV',
                'nombreCorto'=>'IV Coquimbo', 'nombre'=>'Región de Coquimbo'],
            ['cutRegion'=> 5, 'idZona'=>3, 'numero'=>'V',
                'nombreCorto'=>'V Valparaíso', 'nombre'=>'Región de Valparaíso'],
            ['cutRegion'=> 6, 'idZona'=>5, 'numero'=>'VI',
                'nombreCorto'=>"VI O'Higgins", 'nombre'=>'Región del Libertador Gral. Bernardo O’Higgins'],
            ['cutRegion'=> 7, 'idZona'=>5, 'numero'=>'VII',
                'nombreCorto'=>'VII Maule', 'nombre'=>'Región del Maule'],
            ['cutRegion'=> 8, 'idZona'=>6, 'numero'=>'VIII',
                'nombreCorto'=>'VIII Biobío', 'nombre'=>'Región del Biobío'],
            ['cutRegion'=> 9, 'idZona'=>6, 'numero'=>'IX',
                'nombreCorto'=>'IX La Araucanía', 'nombre'=>'Región de La Araucanía'],
            ['cutRegion'=>14, 'idZona'=>7, 'numero'=>'XIV',
                'nombreCorto'=>'XIV Los Ríos', 'nombre'=>'Región de Los Ríos'],
            ['cutRegion'=>10, 'idZona'=>7, 'numero'=>'X',
                'nombreCorto'=>'X Los Lagos', 'nombre'=>'Región de Los Lagos'],
            ['cutRegion'=>11, 'idZona'=>7, 'numero'=>'XI',
                'nombreCorto'=>'XI Aysén', 'nombre'=>'Región de Aysén del Gral. Carlos Ibáñez del Campo'],
            ['cutRegion'=>12, 'idZona'=>7, 'numero'=>'XII',
                'nombreCorto'=>'XII Magallanes y Antártica', 'nombre'=>'Región de Magallanes y de la Antártica Chilena'],
            ['cutRegion'=>13, 'idZona'=>4, 'numero'=>'M',
                'nombreCorto'=>'Metropolitana', 'nombre'=>'Región Metropolitana de Santiago'],
        ]);
    }
}
