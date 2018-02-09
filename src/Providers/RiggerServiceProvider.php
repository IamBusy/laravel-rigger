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
        $this->entityNameSpace = 'App\\'.$this->app['config']->get('rigger.paths.models', 'Entities').'\\';
        $this->repositoryNameSpace = 'App\\'.$this->app['config']->get('rigger.paths.repositories', 'Repositories').'\\';
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
        $className = $this->entityNameSpace.$name;
        $this->app->bindIf($className, function ($app, $parameters) use ($className, $name, $config) {
            if(class_exists($className)) {
                $entity = new $className($parameters);
            } else {
                $table = array_key_exists('table', $config)? $config['table']:Str::plural(Str::lower($name));
                $entity = new Entity($parameters);
                $entity->setTable($table);
                $entity->setEntityName($name);
            }
            return $entity;
        });
    }

    protected function bindRepository($name, $config) {
        $className = $this->repositoryNameSpace.$name.'Repository';
        $this->app->bindIf($className, function ($app, $parameters) use ($name, $config) {
            $repository = new EntityRepository($app);
            $repository->setModelName($this->entityNameSpace.$name);
            $repository->resetModel();
            $repository->setFieldsSearchable(array_key_exists('searchable', $config)?
                $config['searchable']:[]);
            return $repository;
        });
    }
}