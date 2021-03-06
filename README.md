# laravel-rigger
Laravel Rigger is inspired by [facebook/graphql](https://github.com/facebook/graphql), which is
a query language for APIs. But I think its granularity is too thin to use in web development area.
Using entity as query's minimum unit is better to generate a restful back-end swiftly
based on a few necessary configuration, which describe the entities and relations.


# Table of contents
- <a href="#installation">Installation</a>
    - <a href="#composer">Composer</a>
    - <a href="#laravel">Laravel</a>
- <a href="#entity-description">Entity Description</a>
    - <a href="#relations">Relations</a>
    - <a href="#query">Query</a>
    - <a href="#include">Include</a>
    - <a href="#authenticate-and-authorize">Authenticate and Authorize</a>


## Installation

### Composer

```
composer require william/laravel-rigger
```

### Laravel
Only support Laravel >= 5.5, and the ServiceProvider will be attached automatically.
Publish entity config is necessary:

```
php artisan vendor:publish --provider "WilliamWei\LaravelRigger\Providers\LaravelRiggerProvider"
```

This will generate `config/entities.php` and `config/rigger.php` files.
Then register the routes in `RouteServiceProvider`:

```php
/**
 * Define the routes for the application.
 *
 * @return void
 */
public function map()
{
    $this->mapApiRoutes();

    $this->mapWebRoutes();

    Rigger::routes();
}

```

You can use this command to check generated routes.

```
php artisan route:list
```

## Entity Description

All the models/entities wanted to be handled by rigger should be
defined in `config/entities.php`.

### Relations
Support four relations: `hasOne` `belongsTo` `hasMany` `belongsToMany`. Items
in these arrays can be an array or a string. Take `User` as an example, `User` may
have many `Role`s through `user_role` table, so you can define it like this:

```php
'belongsToMany' =>  [
    'roles',
]
```

If you want to modify the joining table, you can use an array:

```php
'belongsToMany' =>  [
    ['roles', 'model_has_roles', 'model_id', 'role_id'],
]

```

All these definitions are consistent to the doc in https://laravel.com/docs/5.5/eloquent-relationships

### Query
This part is powered by [andersao/l5-repository](https://github.com/andersao/l5-repository), and its most exciting
function is allow front-end to perform a dynamic search, filter the data and customize the queries. Check the
[Reference](https://github.com/andersao/l5-repository#using-the-requestcriteria)

Fields which can be searched by this way should be defined in `config/entities.php`. For example:

```php
'user'  =>  [
    'searchableFields'    =>  [
        'name',
    ],
]
```

### Include
This part is powered by [spatie/laravel-fractal](https://github.com/spatie/laravel-fractal), and
it allows front-end to include (a.k.a embedding, nesting or side-loading) relationships for complex data structures.
For example, when you are querying `/users`, you may want to know their roles information at the same time. So
you can add `include` parameters, like `/users?include=roles`:

```json
{
	"data": [{
		"id": 1,
		"name": "rigger",
		"email": "rigger@email.com",
		"password": "rigger",
		"remember_token": "jsdklfjlsdjfl",
		"created_at": "2018-02-09 00:00:00",
		"updated_at": "2018-02-09 00:00:00",
		"roles": {
			"data": [{
				"id": 1,
				"name": "admin",
				"guard_name": "api",
				"created_at": "2018-02-09 00:00:00",
				"updated_at": "2018-02-09 00:00:00",
				"pivot": {
					"model_id": 1,
					"role_id": 1
				}
			}]
		}
	}]
}
```

All the relations defined in `hasOne` `belongsTo` `hasMany` and `belongsToMany` can be fetched by this way.
If you want to add some limitations, you can add  `availableIncludes` array to your entity. If so, keys only
in the array can be parsed even though they defined in relations.


### Authenticate and Authorize
Rigger use [spatie/laravel-permission](https://github.com/spatie/laravel-permission) as basic validator.
There are three control layers in rigger: global layer, entity layer and action layer.
In every layer, there exist two key words `authorize` and `authenticate` to control the behaviour.
`authorize` describes which permissions or roles are needed, and `authenticate` determines whether the user need to
login.

#### authenticate
Only `true` of `false` can be set

#### authorize
```php
'authorized'    =>  [
    'role'          =>  'admin',
    'permission'    =>  'update-user'
]
```

`role` and `permission` are supported. You can alse set placeholder
`${action}` `${resource}` in the permission. For example, if you set `permission`
as `${action}-${resource}` in user item, when visiting `GET /users`, permssion `index-user` will
be required.


#### three control layers
When visiting a specific action, it will check whether this resource has a detailed action-control,
if so, then parse it. If not, it will use entity-control. However, if entity-control doesn't exist,
global-control will active which located in `rigger.php`



