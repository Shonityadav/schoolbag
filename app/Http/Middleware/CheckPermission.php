<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        // Institute admin can do everything
        if ($user->user_type == 1) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, 'Permission denied');
        }

        return $next($request);
    }
}