<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AccessPermissionMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {    
        //pr('okkkkkkkkkkk')   ;
        $response = $next($request);
        //$response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        return $response;
    }
}
