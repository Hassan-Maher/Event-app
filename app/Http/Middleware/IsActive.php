<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(! $request->user()->is_active)
        {
            return ApiResponse::sendResponse(403 , 'You Are Blocked From Admin, to Know Reason Go to Customer Support' , ['is_active' => false]);
        }
        return $next($request);
    }
}
