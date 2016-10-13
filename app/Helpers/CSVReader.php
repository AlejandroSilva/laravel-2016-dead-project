<?php

class CSVReader{

    static function csv_to_array($filename='', $delimiter=';'){
        // el archivo existe y se puede leer
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;


        $matrix = array();
        // se abre el archivo
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $matrix[] = $row;
            }
            // se cierra el archivo
            fclose($handle);
        }
        return $matrix;
    }
    static function getColumn($data, $column, $firstRow=0){
        $maxRows = count($data);

        $col = array();
        for($row=$firstRow; $row<$maxRows; $row++) {
            $col[] = $data[$row][$column];
        }
        return $col;
    }
}