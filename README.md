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
