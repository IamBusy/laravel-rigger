<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:25
 */

namespace WilliamWei\LaravelRigger\Providers;


use Illuminate\Support\ServiceProvider;

class LaravelRiggerProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config.
        $this->mergeConfigFrom(__DIR__ . '/../../config/rigger.php', 'rigger');
        $this->mergeConfigFrom(__DIR__ . '/../../config/entities.php', 'entities');
    }

    /**
     * 在注册后进行服务的启动。
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__.'/../../config/rigger.php' => config_path('rigger.php'),
            __DIR__.'/../../config/entities.php' => config_path('entities.php'),
        ]);
    }
}