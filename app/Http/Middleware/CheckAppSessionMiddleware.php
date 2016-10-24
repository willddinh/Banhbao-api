<?php

namespace App\Http\Middleware;

use Closure;

class CheckAppSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $appSession = $request->header('app-session');
        if(!$appSession)
            return response(json_encode(['message'=>'app session not found']) , 500);

        return $next($request);
    }
}

