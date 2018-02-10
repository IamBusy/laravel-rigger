<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 10/02/2018
 * Time: 18:13
 */

namespace WilliamWei\LaravelRigger;


use Symfony\Component\Process\Exception\InvalidArgumentException;

class Hacker
{
    protected static $definedClass = [];

    public static function createEntity($namespace, $name, $parameters, \Closure $callback) {
        $fullname = $namespace.'\\'.$name;
        if(! in_array($fullname, static::$definedClass)) {
            $definition = "namespace $namespace; class $name extends \\WilliamWei\\LaravelRigger\\Models\\Entity { protected static \$_table;}";
            eval($definition);
            array_push(static::$definedClass, $fullname);
        }
        return $callback(new $fullname($parameters));
    }


    /**
     * @param $classStr , Only support one class definition
     * @return string
     * Naive implement to get class name from its definition
     */
    protected static function parseClassName($classStr) {
        $namespace = '';
        $definitions = explode('class', $classStr, 2);
        if (count($definitions) != 2) {
            throw new InvalidArgumentException('Unsupport class definition');
        }
        $namespaces = explode('namespace', $definitions[0], 2);
        if (count($namespaces) == 2) {
            $namespace = trim(substr($namespaces[1], 0, strpos($namespaces[1], ';')));
        }
        $className = explode(' ', trim($definitions[1]))[0];
        return strlen($namespace)>0? "$namespace\\$className": $className;
    }

}