<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:28
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Entity List
    |--------------------------------------------------------------------------
    |
    | In order to handle the restful request, entities should be defined here
    |
    */

    'user'  =>  [

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        |
        | Reference https://github.com/andersao/l5-repository#using-the-requestcriteria
        |
        */
        'searchableFields'    =>  [
            'name'  =>  'like',
        ],

        // If the key not exist, all the relations will be available to include
        'availableIncludes' =>  [
            'roles',
            'permissions',
        ],


        /*
        |--------------------------------------------------------------------------
        | Relations
        |--------------------------------------------------------------------------
        |
        | You can define this relation by provided an entity name simply, for example:
        |
        | 'hasOne'  =>  ['phone']
        |
        | And of course, a given array which defines foreginKey and localKey is also ok.
        |
        | 'hasOne'  =>  [ ['phone', 'user_id', 'id'] ];
        |
        | All these defines are consistent to the doc in https://laravel.com/docs/5.5/eloquent-relationships
        */
        'hasOne'    =>  [],
        'belongsTo' =>  [],
        'hasMany'   =>  [],
        'belongsToMany' =>  [
            ['users', 'model_has_roles', 'model_id', 'role_id'],
            ['permissions', 'model_has_permissions', 'model_id', 'permission_id'],
        ],

        /*
        |--------------------------------------------------------------------------
        | Routes
        |--------------------------------------------------------------------------
        |  Set a subset of actions the controller should handle instead of the full set of default actions
        */
        'routes'    =>  [],

        /*
        |--------------------------------------------------------------------------
        | Authentication and authorization
        |--------------------------------------------------------------------------
        |  If authenticated is true or an array of actions, then all the action will be authenticated,
        |  'authenticated' =>  ['update', 'destroy']
        |
        |  As default, for each action handler need a permission named {action}-{resource_name},
        |  which like this: update-user
        |
        |  You can also add an array to describe the authentication and authorization of a specific action, for example:
        |
        |   'update'    =>  [
        |       'authenticated' =>  true,
        |       'authorized'    =>  [
        |           'role'          =>  'admin',
        |           'permission'    =>  'update-user'
        |       ],
        |   ]
        |  This config will override the same key in entity if there exist
        */
        'authenticated' =>  false,

        'authorized'    =>  '${action}-${resource}',


    ],

    'role'  =>  [
        'belongsToMany'   =>  [
            ['permissions', 'role_has_permissions'],
            ['users', 'model_has_roles', 'role_id', 'model_id'],
        ],
    ],

    'permission'   =>  [
        'belongsToMany'   =>  [
            ['roles', 'role_has_permissions', 'permission_id', 'role_id'],
            ['users', 'model_has_permissions', 'permission_id', 'model_id'],
        ],
    ]
];