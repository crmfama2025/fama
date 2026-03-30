<?php

namespace App\Http\Middleware;

use App\Models\FcmToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateFcmActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && session()->has('device_id')) {

            FcmToken::where('user_id', $request->user()->id)
                ->where('device_id', session('device_id'))
                ->update([
                    'last_active_at' => now()
                ]);
        }

        return $next($request);
    }
}
