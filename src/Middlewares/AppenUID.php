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


class AppenUID
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
            if ($action == 'store') {
                $resource = lcfirst($request->attributes->get('rigger_entity'));
                if (array_key_exists('user_id', config("entities.$resource"))) {
                    $request[config("entities.$resource")['user_id']] = Auth::user()->id;
                }
            }
        }
        return $next($request);
    }
}