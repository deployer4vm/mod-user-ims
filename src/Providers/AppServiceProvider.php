<?php
namespace hpsynapse\moduser\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use hpsynapse\moduser\Facades\UserAuth;
use hpsynapse\moduser\Console\Commands\UpdateRoleFromJson;

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
        if ($this->app->runningInConsole()) {
            $this->commands([
                UpdateRoleFromJson::class
            ]);
        }
    }
    

    /**
     * Register
     */
    public function register()
    {        
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('UserAuth', UserAuth::class);
        });
    }

}
