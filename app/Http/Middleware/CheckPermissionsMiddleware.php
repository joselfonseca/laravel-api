<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckPermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param String $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!empty($permission)) {
            if (!Auth::user()->hasPermissionTo($permission)) {
                throw new AccessDeniedHttpException('Insufficient Permissions');
            }
        }
        return $next($request);
    }
}
