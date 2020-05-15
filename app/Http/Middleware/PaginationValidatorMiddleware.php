<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaginationValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws ValidationException
     */
    public function handle($request, Closure $next)
    {
        if ($request->filled('per_page')) {
            $validator = validator($request->only(['per_page']), [
                'per_page' => 'integer|min:1|max:500'
            ]);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
        return $next($request);
    }
}
