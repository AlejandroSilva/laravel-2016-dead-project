<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MaestraWOMProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
        $this->app->bind('App\Contracts\MaestraWomContract', function(){
            return new \MaestraWOMService();
        });
    }

    public function register() {
        return ['App\Contracts\MaestraWOMContract'];
    }
}
