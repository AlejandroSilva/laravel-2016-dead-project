<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RespuestaWOMProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
        $this->app->bind('App\Contracts\RespuestaWOMContract', function(){
            return new \RespuestaWOMService();
        });
    }

    public function register() {
        return ['App\Contracts\RespuestaWOMContract'];
    }
}
