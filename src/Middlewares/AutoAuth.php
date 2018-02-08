<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 08/02/2018
 * Time: 00:25
 */

namespace WilliamWei\LaravelRigger\Middlewares;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;


class AutoAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $router = $request->route();
        if($router && $router->action) {
            $action = explode('@',$router->action['controller'])[1];
            $resource = $request->attributes->get('rigger_entity');
            $this->auth($request, $action, $resource);
        }
        return $next($request);
    }

    protected function auth(Request $request, $action, $resource=null) {
        if(! $resource) {
            $resource = $request->attributes->get('rigger_entity');
        }
        if(! array_key_exists($resource, config('entities'))) {
            return $this->parseConfig(config('rigger.auth'), $resource, $action);
        }
        $entityConfig = config('entities.'.$resource);

        // Use auth config of specific action
        if(array_key_exists($action, $entityConfig) &&
            $this->parseConfig($entityConfig[$action], $resource, $action)) {
            return true;
        }
        // Use auth config of this entity
        else if($this->parseConfig($entityConfig, $resource, $action)){
            return true;
        }
        // Use global auth config
        else {
            return $this->parseConfig(config('rigger.auth'), $resource, $action);
        }
    }


    protected function parseConfig(array $config, $resource, $action) {
        if(array_key_exists('authorized', $config)) {
            if(array_key_exists('role', $config['authorized'])) {
                $this->authorizeRole($config['authorized']['role']);
            }
            if(array_key_exists('permission', $config['authorized'])) {
                $this->authorizePermission(
                    str_replace(['${resource}','${action}'],
                        [$resource, $action],
                        $config['authorized']['permission']));
            }
            return true;
        } else if(array_key_exists('authenticated', $config)) {
            if($config['authenticated']) {
                if (Auth::guest()) {
                    throw UnauthorizedException::notLoggedIn();
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $role
     * The user should have one role as least
     */
    protected function authorizeRole($role) {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if(count($roles) > 0) {
            if (! Auth::user()->hasAnyRole($roles)) {
                throw UnauthorizedException::forRoles($roles);
            }
        }
    }

    /**
     * @param $permission
     * The user should have at least one permission
     */
    protected function authorizePermission($permission) {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if (Auth::user()->can($permission)) {
                return ;
            }
        }
        throw UnauthorizedException::forPermissions($permissions);
    }

}