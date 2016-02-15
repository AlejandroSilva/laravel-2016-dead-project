<?php

use Illuminate\Database\Seeder;

class LocalesTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $parsedData = null;
        DB::transaction(function() {
            $this->parseAndInsert('/home/asilva/Escritorio/localesFCV.csv');
            $this->parseAndInsert('/home/asilva/Escritorio/localesPreunic.csv');
        });
    }

    public static function parseAndInsert($file){
        //public_path().'/csv/niger.csv';
        $data = CSVSeeder::csv_to_array($file);

        array_map(function ($row){
            $now = \Carbon\Carbon::now();
            // Local
            $local = [// FK
                'idCliente' => $row['idCliente'],
                'idFormatoLocal' => $row['idFormatoLocal'],

                // otros campos
                'numero' => $row['numero'],
                'nombre' => $row['nombre'],
                'horaApertura' => $row['horaApertura'],
                'horaCierre' => $row['horaCierre'],
                'emailContacto' => $row['emailContacto'],
                'codArea1' => $row['codArea1'],
                'codArea2' => $row['codArea2'],
                'telefono1' => $row['telefono1'],
                'telefono2' => $row['telefono2'],
                'stock' => $row['stock'],
                'fechaStock' => $row['fechaActualizacionStock'],
                'created_at'=>$now,
                'updated_at'=> $now
            ];
            $idLocal = DB::table('locales')->insertGetId($local);

            // Direccion
            $direccion = [// Composite key (PK & FK)
                'idLocal' => $idLocal, // FK
                'cutComuna' => $row['cutComuna'], // otros campos
                'direccion' => $row['direccion'],
                'referencia' => $row['referencia'],
                'gmapShortUrl' => $row['gmapShortUrl'],
                'gmapIframeUrl' => $row['gmapIframeUrl'],
                'created_at'=>$now,
                'updated_at'=>$now
            ];
            DB::table('direcciones')->insert($direccion);
        }, $data);
    }
}
