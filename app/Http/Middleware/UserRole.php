<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserRole
{
    /**
     * Check Role User
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed $isRole
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $isRole): Response
    {
        $user = Auth::user();

        $roles = explode(',', $isRole);

        if (! in_array($user->role, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
