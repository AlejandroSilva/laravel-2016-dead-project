<?php namespace App\Services;

// Contracts
use App\Auditorias;
use App\Contracts\MuestrasFCVContract;
use Illuminate\Support\Facades\File;

// Modelos

class MuestrasFCVService implements MuestrasFCVContract {

    public function agregarArchivoIrd($idAuditoria, $fullPath, $originalFilename){
        $auditoria = Auditorias::find($idAuditoria);
        if(!$auditoria)
            return $this->_error('idAuditoria', 'auditoria no encontrada', 400);
        if(!File::exists($fullPath))
            return $this->_error('archivo', 'el archivo indicado no existe', 400);

        $filename = File::name($fullPath);
        $destFolder = Auditorias::getPathMuestras();
        if(!File::exists($destFolder))
            File::makeDirectory($destFolder);

        File::move($fullPath, $destFolder.$filename);
        $auditoria->nombreArchivoIrd = $filename;
        $auditoria->nombreOriginalIrd = $originalFilename;
        $auditoria->save();
        return $destFolder.$filename;
    }

    public function agregarArchivoVencimiento($idAuditoria, $fullPath, $originalFilename){
        $auditoria = Auditorias::find($idAuditoria);
        if(!$auditoria)
            return $this->_error('idAuditoria', 'auditoria no encontrada', 400);
        if(!File::exists($fullPath))
            return $this->_error('archivo', 'el archivo indicado no existe', 400);

        $filename = File::name($fullPath);
        $destFolder = Auditorias::getPathMuestras();
        if(!File::exists($destFolder))
            File::makeDirectory($destFolder);

        File::move($fullPath, $destFolder.$filename);
        $auditoria->nombreArchivoVencimiento = $filename;
        $auditoria->nombreOriginalVencimiento = $originalFilename;
        $auditoria->save();
        return $destFolder.$filename;
    }

    // privados
    private function _error($campo, $mensaje, $codigo){
        return (object)[
            'campo'=>$campo,
            'mensaje' => $mensaje,
            'error'=>[
                "$campo"=>$mensaje
            ],
            'codigo'=>$codigo
        ];
    }
}