<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 09/02/2018
 * Time: 16:50
 */

namespace WilliamWei\LaravelRigger\Models;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Str;

trait Attributes
{
    use HasAttributes;

    /**
     * Override to support dynamic relation
     * @param string $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }

        if (method_exists($this, 'getRelationFromRigger')) {
            return $this->getRelationFromRigger($key);
        }
    }


    /**
     * Generate relation from configuration automatically
     * @param $key
     * @return mixed
     */
    public function getRelationFromRigger($key) {
        $cfg = config("entities.". Str::singular($this->getTable()));
        foreach (['hasOne', 'belongsTo', 'hasMany', 'belongsToMany'] as $item) {
            if (array_key_exists($item, $cfg)) {
                foreach ($cfg[$item] as $relation) {
                    if (is_array($relation) && count($relation) && $relation[0] == $key){
                        return call_user_func_array([$this, $item], $relation);
                    } elseif ((!is_array($relation) && $relation == $key)) {
                        return call_user_func_array([$this, $item], [$relation]);
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    abstract public function getTable();


}