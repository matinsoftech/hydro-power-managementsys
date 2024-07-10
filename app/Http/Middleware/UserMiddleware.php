<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->user_type == 'User') {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return redirect()->route('login');
    }
}
