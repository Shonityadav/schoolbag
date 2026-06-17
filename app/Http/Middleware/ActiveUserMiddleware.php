<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActiveUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Only update if it's been more than 2 minutes since last update
            // to avoid hitting the DB on literally every request
            if (!$user->last_active_at || $user->last_active_at->diffInMinutes(now()) >= 2) {
                // We use DB facade to avoid firing model events/timestamps just for this
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['last_active_at' => now()]);
            }
        }

        return $next($request);
    }
}
