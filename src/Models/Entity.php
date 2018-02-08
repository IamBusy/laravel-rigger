<?php

/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:33
 */
namespace WilliamWei\LaravelRigger\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function setTable($table)
    {
        static::$_table = $table;
    }

    public function getTable()
    {
        return static::$_table;
    }
}