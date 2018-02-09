<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 09/02/2018
 * Time: 16:50
 */

namespace WilliamWei\LaravelRigger\Models;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;

trait Attributes
{
    use HasAttributes;

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


}