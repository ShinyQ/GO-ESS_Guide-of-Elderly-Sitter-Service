<?php

namespace App\Http\Middleware;

use Closure;
use Api;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if(empty(auth()->guard('api')->user())){
            return Api::apiRespond(401, []);
        }

        return $next($request);
    }
}
