<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MaestraFCVProvider extends ServiceProvider {

    protected $defer = true;

    public function boot() {
        $this->app->bind('App\Contracts\NominaServiceContract', function(){
            return new \MaestraFCVService();
        });
    }

    public function register() {
        return ['App\Contracts\MaestraFCVContract'];
    }
}
