<?php
namespace hpsynapse\moduser\Providers;

use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    // private $config;
    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot()
    {
        $this->app['router']->pushMiddlewareToGroup('web', \hpsynapse\moduser\Middleware\InitAuthWeb::class);
        $this->app['router']->pushMiddlewareToGroup('api', \hpsynapse\moduser\Middleware\InitAuthAPI::class);
    }
    

    /**
     * Register
     */
    public function register()
    {
        
    }

}
