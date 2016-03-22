<?php

class CSVSeeder{

    static function csv_to_array($filename='', $delimiter=','){
        // el archivo existe y se puede leer
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        // se abre el archivo
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                // la primera fila se asigna como header
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            // se cierra el archivo
            fclose($handle);
        }
        return $data;
    }
}