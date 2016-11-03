<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NominaServiceProvider extends ServiceProvider {

    protected $defer = true;

    public function boot(){}

    public function register() {
        $this->app->bind('App\Contracts\NominaServiceContract', function(){
            return new \NominaService();
        });
    }
    public function provides() {
        return ['App\Contracts\NominaServiceContract'];
    }

}
