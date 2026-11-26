<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CITokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authSessionToken = session()->get('auth_token');
        $userToken = "";

        if ($request->get('aysToken')){
            $userToken = $request->get('aysToken');
        }elseif ($request->get('ci_token')){
            $userToken = $request->get('ci_token');
        }
        if (!$request->ajax()){
            if($userToken !== $authSessionToken){
                return abort(403, 'Unauthorized User!');
            }
        }

        return $next($request);
    }
}
