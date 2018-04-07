<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 08/02/2018
 * Time: 00:14
 */

namespace WilliamWei\LaravelRigger\Middlewares;


use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ExtractEntity
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
        $path = $request->path();
        if (strlen(config('rigger.api.prefix', '')) > 0) {
            $startPos = strpos($path, config('rigger.api.prefix'));
            $path = substr($path, $startPos + strlen(config('rigger.api.prefix')) + 1);
        }
        $parts = explode('/', $path);
        if (count($parts) == 0) {
            throw new NotFoundResourceException();
        }
        $name = $parts[0];
        // Upper the first char of entity name in url
        $request->attributes->set('rigger_entity', Str::ucfirst(Str::singular($name)));
        return $next($request);
    }

}