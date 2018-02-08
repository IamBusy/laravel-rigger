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
        // Register bindings.
        $this->registerBindings();
        // Config path.
        $rigger_path = __DIR__ . '/../../../config/rigger.php';
        $entities_path = __DIR__ . '/../../../config/entities.php';
        // Merge config.
        $this->mergeConfigFrom($rigger_path, 'rigger');
        $this->mergeConfigFrom($entities_path, 'entities');
    }

    /**
     * Register the bindings.
     */
    protected function registerBindings()
    {
        // FileSystem.
        $this->app->instance('FileSystem', new Filesystem());
        // Composer.
        $this->app->bind('Composer', function ($app)
        {
            return new Composer($app['FileSystem']);
        });
        // Repository creator.
        $this->app->singleton('RepositoryCreator', function ($app)
        {
            return new RepositoryCreator($app['FileSystem']);
        });
        // Criteria creator.
        $this->app->singleton('CriteriaCreator', function ($app)
        {
            return new CriteriaCreator($app['FileSystem']);
        });
    }

}