# laravel-rigger
Laravel Rigger is used to generate a restful back-end swiftly
based on a few necessary configuration, which describe the entity
relations.

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

Now, register the routes in `AuthServiceProvider`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use WilliamWei\LaravelRigger\Rigger;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Rigger::routes();
    }
}

```

This will generate routes according to your definitions in `config/entities.php`.
Since there are several items, if your database has been seeded, you can visit `localhost/rigger/users` without 
writing any codes:

```json
[{
	"id": 1,
	"name": "rigger",
	"email": "rigger@email.com",
	"password": "rigger",
	"remember_token": "jsdklfjlsdjfl",
	"created_at": "2018-02-09 00:00:00",
	"updated_at": "2018-02-09 00:00:00"
}, {
	"id": 2,
	"name": "rigger-admin",
	"email": "rigger-admin@email.com",
	"password": "rigger-admin",
	"remember_token": "jsdklfjlsdjfl",
	"created_at": "2018-02-09 00:00:00",
	"updated_at": "2018-02-09 00:00:00"
}]
```




