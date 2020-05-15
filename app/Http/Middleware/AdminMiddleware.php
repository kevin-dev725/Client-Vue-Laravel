<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::user()->isAdmin()) {
            throw new AuthorizationException();
        }
        return $next($request);
    }
}
