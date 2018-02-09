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

class Entity extends Model
{
    use Attributes;

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


    /**
     * Generate relation from configuration automatically
     * @param $key
     * @return mixed
     */
    public function getRelationFromRigger($key) {
        $cfg = config("entities.". Str::singular(static::getTable()));
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
}