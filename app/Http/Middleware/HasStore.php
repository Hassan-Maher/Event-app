<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next):Response
{
    if (!$request->user()->store) {
        return ApiResponse::sendResponse(403 , 'please complete store data' , ['has_store' => false]);
    }

    return $next($request);
}

}
