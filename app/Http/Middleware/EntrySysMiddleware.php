<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EntrySys;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EntrySysMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $entrySysExists = EntrySys::where('date', now()->format('Y-m-d'))->latest()->first();

        if($entrySysExists) {

            if($entrySysExists->user_id != auth()->user()->id && $entrySysExists->check_out_time == null){

                if($request->expectsJson()) {

                    return response()->json([
                        'success' => false,
                        'data' => 'Someone has already checked in please let user check out first.',
                        'message' => 'Someone has already checked in please let user check out first.'
                    ]);

                }

                abort(403, 'Someone has already checked in please let user check out first.');
            }
        }

        return $next($request);
    }
}
