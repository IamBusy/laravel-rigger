<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:39
 */

namespace WilliamWei\LaravelRigger\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use WilliamWei\LaravelRigger\Hacker;
use WilliamWei\LaravelRigger\Models\Entity;
use WilliamWei\LaravelRigger\Repositories\EntityRepository;

class RiggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    protected $entityNameSpace;
    protected $repositoryNameSpace;


    public function boot()
    {
        $this->entityNameSpace = 'App\\'.$this->app['config']->get('rigger.paths.models', 'Entities');
        $this->repositoryNameSpace = 'App\\'.$this->app['config']->get('rigger.paths.repositories', 'Repositories');
        foreach ($this->app['config']->get('entities', []) as $name => $config) {
            $className = Str::ucfirst($name);
            $this->bindEntity($className, $config);
            $this->bindRepository($className, $config);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function bindEntity($name, $config) {
        $namespace = $this->entityNameSpace;
        $className = $this->entityNameSpace.'\\'.$name;
        if (! class_exists($className)) {
            $this->app->bindIf($className, function ($app, $parameters) use ($namespace, $name, $config) {
                $table = array_key_exists('table', $config)? $config['table']:Str::plural(Str::lower($name));
                $entity = Hacker::createEntity($namespace, $name, $parameters, function ($instance) use ($table) {
                    $instance->setTable($table);
                    return $instance;
                });
                $entity->setEntityName(lcfirst($name));
                return $entity;
            });
        }
    }

    protected function bindRepository($name, $config) {
        $className = $this->repositoryNameSpace.'\\'.$name.'Repository';
        $this->app->bindIf($className, function ($app, $parameters) use ($name, $config) {
            $repository = new EntityRepository($app);
            $repository->setModelName($this->entityNameSpace.'\\'.$name);
            $repository->resetModel();
            $repository->setFieldsSearchable(array_key_exists('searchableFields', $config)?
                $config['searchableFields']:[]);
            return $repository;
        });
    }
}