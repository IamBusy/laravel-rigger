<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 09/02/2018
 * Time: 12:58
 */

namespace WilliamWei\LaravelRigger;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use WilliamWei\LaravelRigger\Controllers\Controller;


class Rigger
{

    public static function routes()
    {
        $config = app('config');
        $controllerNameSpace = 'App\\'.$config->get('rigger.paths.controllers').'\\';
        Route::prefix($config->get('rigger.api.prefix', ''))
            ->middleware($config->get('rigger.api.middlewares', []))
            ->group(function() use ($config, $controllerNameSpace) {
                foreach ($config->get('entities', []) as $name => $cfg) {
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