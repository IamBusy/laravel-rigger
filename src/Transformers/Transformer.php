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
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    protected $availableIncludesInitialized = false;
    protected $availableIncludes = [];

    protected $table;

    /**
     * @param Model $model
     * @return array
     * @description
     *  this method will be called before getAvailableIncludes, so we can save table info
     *  here, which will be used in getAvailableIncludes.
     */
    public function transform(Model $model)
    {
        $this->table = Str::singular($model->getTable());
        return $model->toArray();
    }

    /**
     * Getter for availableIncludes.
     *
     * @return array
     */
    public function getAvailableIncludes()
    {
        if ($this->availableIncludesInitialized) {
            return $this->availableIncludes;
        }
        $entityCfg = config("entities.$this->table");
        if (array_key_exists('availableIncludes', $entityCfg)) {
            $this->availableIncludes = $entityCfg['availableIncludes'];
        } else {
            $includes = [];
            foreach (['hasOne', 'belongsTo', 'hasMany', 'belongsToMany'] as $item) {
                if (array_key_exists($item, $entityCfg)) {
                    foreach ($entityCfg[$item] as $relation) {
                        // TODO relation name may be a class name rather than a entity name
                        // TODO Should initialize in service provider?
                        if (is_array($relation)) {
                            if (!count($relation)) continue;
                            array_push($includes, $relation[0]);
                        } else {
                            if (!strlen($relation)) continue;
                            array_push($includes, $relation);
                        }
                    }
                }
            }
            $this->availableIncludes = $includes;
        }
        $this->availableIncludesInitialized = true;
        return $this->availableIncludes;
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