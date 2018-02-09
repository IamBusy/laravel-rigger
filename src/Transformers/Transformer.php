<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 09/02/2018
 * Time: 15:22
 */

namespace WilliamWei\LaravelRigger\Transformers;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    protected $availableIncludes = ['roles','permissions'];

    public function transform(Model $model)
    {
        return $model->toArray();
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'include')) {
            $modelMethod = lcfirst(substr($name, strlen('include')));
            $resources = $arguments[0]->$modelMethod;
            //dd($resources);
            if(! $resources) {
                return null;
            }
            // TODO use NameTransformer
            if (is_array($resources) || $resources instanceof Collection) {
                return $this->collection($resources, new Transformer());
            } else {
                return $this->item($resources, new Transformer());
            }
        }
    }
}