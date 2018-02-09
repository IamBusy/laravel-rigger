<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 09/02/2018
 * Time: 15:22
 */

namespace WilliamWei\LaravelRigger\Transformers;


use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    public function transform($model)
    {
        return $model->toArray();
    }

    public function __call($name, $arguments)
    {
        if (starts_with($name, 'include')) {
            $modelMethod = substr($name, strlen('include'));
            $resources = $arguments[0]->$modelMethod;
            if(! $resources) {
                return null;
            }
            // TODO use NameTransformer
            if (is_array($resources)) {
                return $this->collection($resources, new Transformer());
            } else {
                return $this->item($resources, new Transformer());
            }
        }
    }
}