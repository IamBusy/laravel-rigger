<?php

/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:52
 */
namespace WilliamWei\LaravelRigger\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use WilliamWei\LaravelRigger\Models\Entity;

class EntityRepository extends BaseRepository
{

    protected $modelName;


    public function setModelName($name) {
        $this->modelName = $name;
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        if($this->modelName) {
            return $this->modelName;
        }
        return Entity::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria($this->app->make(RequestCriteria::class));
    }

}