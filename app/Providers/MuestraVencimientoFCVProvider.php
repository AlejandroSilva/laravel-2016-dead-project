<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MuestraVencimientoFCVProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
        $this->app->bind('App\Contracts\MuestraVencimientoFCVServiceContract', function(){
            return new \MuestraVencimientoFCVService();
        });
    }

    public function register() {
        return ['App\Contracts\MuestraVencimientoFCVServiceContract'];
    }
}
