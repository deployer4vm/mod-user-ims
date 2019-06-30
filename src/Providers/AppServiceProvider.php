<?php

namespace hpsynapse\moduser\Providers;

//use Auth;//uncomment jika nanti menggunakan custom user provider
//use BSSystem\MODWebAuth\Services\UserAuthProvider;//uncomment jika nanti menggunakan custom user provider

use Illuminate\Support\ServiceProvider;
// use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app['router']->pushMiddlewareToGroup('web', \BSSystem\MODWebAuth\Middleware\BSAccountCenterSessionCheck::class);
               
        //uncomment jika nanti menggunakan custom user provider
//        Auth::provider('bssystem_auth', function($app, array $config) {
//            return new UserAuthProvider();
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {        
        // $this->app->booting(function() {
        //     $loader = AliasLoader::getInstance();
        //     $loader->alias('ACAuth', ACAuth::class);
        // });
        
        // $this->loadConfig();
    }
    
    
    private function loadConfig()
    {
        
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];
        
        //jika config sudah diset di aplikasi
        if(isset($config['bssystem']['webauth'])){
            
            $config1 = require __DIR__ . '/../config/config.php';
            $config2 = $config['bssystem']['webauth'];
            $config->set('bssystem.webauth', array_merge($config1,$config2));          
        }else{     
            $config->set('bssystem.webauth', require __DIR__ . '/../config/config.php');
            
        }
    }
}
