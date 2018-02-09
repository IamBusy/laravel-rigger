<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 07/02/2018
 * Time: 23:27
 */

return [

    'api'   =>  [
        'prefix'    =>  env('RIGGER_API_PREFIX','rigger'),
        'middlewares'   =>  [
            'api',
            \WilliamWei\LaravelRigger\Middlewares\ExtractEntity::class,
        ],
    ],

    'paths'  =>  [
        'models'       => 'Entities',
        'controllers'  => 'Http/Controllers',
        'repositories' => 'Repositories',
    ],


    /*
    |--------------------------------------------------------------------------
    | Global Authentication and Authorization
    |--------------------------------------------------------------------------
    | When define permission, you can use variables ${action}\${resource}, this will
    | be replaced in running time
    */
    'auth'  =>  [
        'authenticated' =>  true,
        'authorized'    =>  [
            'permission'    =>  '${action}-${resource}'
        ]
    ]
];