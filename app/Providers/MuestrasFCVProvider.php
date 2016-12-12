<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MuestrasFCVProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
        $this->app->bind('App\Contracts\MuestrasFCVContract', function(){
            return new \MuestrasFCVService();
        });
    }

    public function register() {
        return ['App\Contracts\MuestrasFCVContract'];
    }
}
