<?php

/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:33
 */
namespace WilliamWei\LaravelRigger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\InvalidArgumentException;

class Entity extends Model
{

    /*
    |--------------------------------------------------------------------------
    | Dynamic table
    |--------------------------------------------------------------------------
    |
    | Ref: https://stackoverflow.com/questions/18044577/laravel-4-dynamic-table-names-using-settable
    |
    */
    protected static $_table;

    // Key in entities.php
    protected $entityName;

    protected $availableRelations;

    public function setTable($table)
    {
        static::$_table = $table;
    }

    public function getTable()
    {
        return static::$_table;
    }

    public function setEntityName($name) {
        $this->entityName = $name;
    }

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

        // If the model has getRelationshipFromRigger method, then call it
        if (method_exists($this, 'getRelationshipFromRigger')) {
            return $this->getRelationshipFromRigger($key);
        }
    }


    /**
     * Generate relation from configuration automatically
     * @param $key string, may be singular or plural
     * @return mixed
     * @description
     *  relations defined in entities.php support two types. The first one is use another
     *  entity name such as 'user' or 'permission'. The other is a class name with full namespace
     *  such as 'App\Models\User'. It will be distinguished by the first char, upper or lower.
     *
     */
    public function getRelationshipFromRigger($key) {
        $cfg = config("entities.". Str::singular(static::getTable()));
        $singularKey = Str::singular($key);
        foreach (['hasOne', 'belongsTo', 'hasMany', 'belongsToMany'] as $item) {
            if (array_key_exists($item, $cfg)) {
                foreach ($cfg[$item] as $relation) {

                    if (is_array($relation)) {
                        if (!count($relation)) continue;
                        $relation[0] = $this->classNameToEntityName($relation[0]);
                    } else {
                        if (!strlen($relation)) continue;
                        $relation = [$this->classNameToEntityName($relation)];
                    }

                    if ($relation[0] == $singularKey) {
                        $relation[0] = $this->entityNameToClassName($relation[0]);
                        $relationShip = call_user_func_array([$this, $item], $relation);
                        return tap($relationShip->getResults(), function ($results) use ($key) {
                            $this->setRelation($key, $results);
                        });

                    }
                }
            }
        }
    }

    /**
     * @param $entityName , defined in entities.php
     * @return string
     */
    protected function entityNameToClassName($entityName) {
        if (!strlen($entityName)) {
            throw new InvalidArgumentException('Invalid entity name');
        }
        if (preg_match('/^[a-z]+$/', $entityName[0])) {
            return 'App\\'.config('rigger.paths.models').'\\'.Str::singular(ucfirst($entityName));
        }
        return $entityName;
    }

    protected function classNameToEntityName($className) {
        if (!strlen($className)) {
            throw new InvalidArgumentException('Invalid entity name');
        }
        if (preg_match('/^[a-z]+$/', $className[0])) {
            return Str::singular($className);
        }
        return Str::singular(lcfirst(end(explode('\\', $className))));
    }

    protected function newRelatedInstance($class)
    {
        if (class_exists($class)) {
            $instance = new $class;
        } else {
            $instance = app($class);
        }
        return tap($instance, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        return Str::snake(Str::singular($this->getTable())).'_'.$this->primaryKey;
    }
}