<?php

namespace WilliamWei\LaravelRigger\Providers;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use WilliamWei\LaravelRigger\Controllers\Controller;

/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 22:36
 */
class RouterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->routesAreCached()) {
            $this->mapDynamicRoutes();
        }
    }


    protected function mapDynamicRoutes() {
        $config = $this->app['config'];
        $controllerNameSpace = 'App\\'.$config->get('rigger.paths.controllers').'\\';
        Route::prefix($config->get('rigger.api.prefix', ''))
            ->middleware($config->get('rigger.api.middlewares', []))
            ->group(function() use ($config, $controllerNameSpace) {
                foreach ($config->get('entity', []) as $name => $cfg) {
                    if(! class_exists($controllerNameSpace.$name.'Controller')) {
                        $options = [];
                        if(array_key_exists('routes', $cfg)) {
                            $options = $cfg['routes'];
                        }
                        Route::apiResource(Str::plural($name), Controller::class, $options);
                    }
                }
            });
    }
}